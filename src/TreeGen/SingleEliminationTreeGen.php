<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Exceptions\TreeGenerationException;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\PreliminaryFight;
use Xoco70\LaravelTournaments\Models\SingleEliminationFight;

abstract class SingleEliminationTreeGen extends TreeGen
{
    /**
     * Calculate the Byes need to fill the Championship Tree.
     *
     * @param $fighters
     *
     * @return Collection
     */
    protected function getByeGroup($fighters)
    {
        $fighterCount = $fighters->count();
        $firstRoundGroupSize = $this->firstRoundGroupSize();
        $treeSize = $this->getTreeSize($fighterCount, $firstRoundGroupSize);
        $byeCount = $treeSize - $fighterCount;

        return $this->createByeGroup($byeCount);
    }

    /**
     * Save Groups with their parent info.
     *
     * @param int $numRounds
     * @param int $numFighters
     */
    protected function pushGroups($numRounds, $numFighters)
    {
        // TODO Here is where you should change when enable several winners for preliminary
        for ($roundNumber = 2; $roundNumber <= $numRounds + 1; $roundNumber++) {
            // From last match to first match
            $maxMatches = ($numFighters / pow(2, $roundNumber));

            for ($matchNumber = 1; $matchNumber <= $maxMatches; $matchNumber++) {
                $fighters = $this->createByeGroup(2);
                $group = $this->saveGroup($matchNumber, $roundNumber, null);
                $this->syncGroup($group, $fighters);
            }
        }
        // Third place Group
        if ($numFighters >= $this->championship->getGroupSize() * 2) {
            $fighters = $this->createByeGroup(2);

            $group = $this->saveGroup($maxMatches, $numRounds, null);

            $this->syncGroup($group, $fighters);
        }
    }

    /**
     * Create empty groups after round 1.
     *
     * @param $numFighters
     */
    protected function pushEmptyGroupsToTree($numFighters)
    {
        if ($this->championship->hasPreliminary()) {
            /* Should add * prelimWinner but it add complexity about parent / children in tree
            */
            $numFightersElim = $numFighters / $this->settings->preliminaryGroupSize * 2;
            // We calculate how much rounds we will have
            $numRounds = intval(log($numFightersElim, 2)); // 3 rounds, but begining from round 2 ( ie => 4)
            return $this->pushGroups($numRounds, $numFightersElim);
        }
        // We calculate how much rounds we will have
        $numRounds = $this->getNumRounds($numFighters);

        return $this->pushGroups($numRounds, $numFighters);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle.
     *
     * @param $fightersByEntity
     *
     * @return Collection|null
     */
    protected function chunkAndShuffle(Collection $fightersByEntity)
    {
        //TODO Should Pull down to know if team or competitor
        if ($this->championship->hasPreliminary()) {
            return (new PlayOffCompetitorTreeGen($this->championship, null))->chunkAndShuffle($fightersByEntity);
        }
        $fightersGroup = null;

        $fightersGroup = $fightersByEntity->chunk(2);
        if (!app()->runningUnitTests()) {
            $fightersGroup = $fightersGroup->shuffle();
        }

        return $fightersGroup;
    }

    /**
     * Generate First Round Fights.
     */
    protected function generateFights()
    {

        //  First Round Fights
        $settings = $this->settings;
        $initialRound = 1;
        // Very specific case to common case : Preliminary with 3 fighters
        if ($this->championship->hasPreliminary() && $settings->preliminaryGroupSize == 3) {
            // First we make all first fights of all groups
            // Then we make all second fights of all groups
            // Then we make all third fights of all groups
            $groups = $this->championship->groupsByRound(1)->get();
            for ($numFight = 1; $numFight <= $settings->preliminaryGroupSize; $numFight++) {
                $fight = new PreliminaryFight();
                $fight->saveFights($groups, $numFight);
            }
            $initialRound++;
        }
        // Save Next rounds
        $fight = new SingleEliminationFight();
        $fight->saveFights($this->championship, $initialRound);
    }

    /**
     * Return number of rounds for the tree based on fighter count.
     *
     * @param $numFighters
     *
     * @return int
     */
    protected function getNumRounds($numFighters)
    {
        return intval(log($numFighters / $this->firstRoundGroupSize() * 2, 2));
    }

    private function firstRoundGroupSize()
    {
        return $this->championship->hasPreliminary()
            ? $this->settings->preliminaryGroupSize
            : 2;
    }

    protected function generateAllTrees()
    {
        $this->minFightersCheck();
        $usersByArea = $this->getFightersByArea();
        $numFighters = count($usersByArea->collapse());
        $this->generateGroupsForRound($usersByArea, 1);
        $this->pushEmptyGroupsToTree($numFighters);
        $this->addParentToChildren($numFighters);
    }

    /**
     * @param Collection $usersByArea
     * @param $round
     */
    public function generateGroupsForRound(Collection $usersByArea, $round)
    {
        $order = 1;
        foreach ($usersByArea as $fightersByEntity) {
            // Chunking to make small round robin groups
            $chunkedFighters = $this->chunkAndShuffle($fightersByEntity);
            foreach ($chunkedFighters as $fighters) {
                $fighters = $fighters->pluck('id');
                if (!app()->runningUnitTests()) {
                    $fighters = $fighters->shuffle();
                }
                $group = $this->saveGroup($order, $round, null);
                $this->syncGroup($group, $fighters);
                $order++;
            }
        }
    }

    /**
     * Check if there is enough fighters, throw exception otherwise.
     *
     * @throws TreeGenerationException
     */
    private function minFightersCheck()
    {
        $fighters = $this->getFighters();
        $areas = $this->settings->fightingAreas;
        $fighterType = $this->championship->category->isTeam
            ? trans_choice('laravel-tournaments::core.team', 2)
            : trans_choice('laravel-tournaments::core.competitor', 2);

        $minFighterCount = $fighters->count() / $areas;

        if ($this->settings->hasPreliminary && $fighters->count() / ($this->settings->preliminaryGroupSize * $areas) < 1) {
            throw new TreeGenerationException(trans('laravel-tournaments::core.min_competitor_required', [
                'number'       => $this->settings->preliminaryGroupSize * $areas,
                'fighter_type' => $fighterType,
            ]), 422);
        }

        if ($minFighterCount < ChampionshipSettings::MIN_COMPETITORS_BY_AREA) {
            throw new TreeGenerationException(trans('laravel-tournaments::core.min_competitor_required', [
                'number'       => ChampionshipSettings::MIN_COMPETITORS_BY_AREA,
                'fighter_type' => $fighterType,
            ]), 422);
        }
    }
}
