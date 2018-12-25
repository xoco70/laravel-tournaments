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
     * Calculate the Byes needed to fill the Championship Tree.
     *
     * @param $fighters
     *
     * @return Collection
     */
    protected function getByeGroup($fighters)
    {
        $fighterCount = $fighters->count();
        $firstRoundGroupSize = $this->firstRoundGroupSize(); // Get the size of groups in the first round (2,3,4)
        // Max number of fighters for the first round
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
    protected function chunk(Collection $fightersByEntity)
    {
        //TODO Should Pull down to know if team or competitor
        if ($this->championship->hasPreliminary()) {
            return (new PlayOffCompetitorTreeGen($this->championship, null))->chunk($fightersByEntity);
        }
        $fightersGroup = $fightersByEntity->chunk(2);
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
            foreach ($groups as $numGroup => $group) {
                for ($numFight = 1; $numFight <= $settings->preliminaryGroupSize; $numFight++) {
                    $fight = new PreliminaryFight();
                    $order = $numGroup * $settings->preliminaryGroupSize + $numFight;
                    $fight->saveFight2($group, $numFight, $order);
                }
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

    /**
     * Get the group size for the first row
     *
     * @return int - return 2 if not preliminary, 3,4,5 otherwise
     */
    private function firstRoundGroupSize()
    {
        return $this->championship->hasPreliminary()
            ? $this->settings->preliminaryGroupSize
            : 2;
    }

    /**
     * Generate all the groups, and assign figthers to group
     * @throws TreeGenerationException
     */
    protected function generateAllTrees()
    {
        $this->minFightersCheck(); // Check there is enough fighters to generate trees
        $usersByArea = $this->getFightersByArea(); // Get fighters by area (reparted by entities and filled with byes)
        $this->generateGroupsForRound($usersByArea, 1); // Generate all groups for round 1
        $numFighters = count($usersByArea->collapse());
        $this->pushEmptyGroupsToTree($numFighters); // Fill tree with empty groups
        $this->addParentToChildren($numFighters); // Build the entire tree and fill the next rounds if possible
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
            $chunkedFighters = $this->chunk($fightersByEntity);
//            dd($chunkedFighters->toArray());
            foreach ($chunkedFighters as $fighters) {
                $fighters = $fighters->pluck('id');
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
                'number' => $this->settings->preliminaryGroupSize * $areas,
                'fighter_type' => $fighterType,
            ]), 422);
        }

        if ($minFighterCount < ChampionshipSettings::MIN_COMPETITORS_BY_AREA) {
            throw new TreeGenerationException(trans('laravel-tournaments::core.min_competitor_required', [
                'number' => ChampionshipSettings::MIN_COMPETITORS_BY_AREA,
                'fighter_type' => $fighterType,
            ]), 422);
        }
    }

    /**
     * Insert byes group alternated with full groups.
     *
     * @param Collection $fighters - List of fighters
     * @param integer $numByeTotal - Quantity of byes to insert
     * @return Collection - Full list of fighters including byes
     */
    private function insertByes(Collection $fighters, $numByeTotal)
    {
        $bye = $this->createByeFighter();
        $groupSize = $this->firstRoundGroupSize();
        $frequency = $groupSize != 0
            ? (int)floor(count($fighters) / $groupSize / $groupSize)
            : -1;
        if ($frequency < $groupSize) {
            $frequency = $groupSize;
        }

        $newFighters = new Collection();
        $count = 0;
        $byeCount = 0;
        foreach ($fighters as $fighter) {
            // Each $frequency(3) try to insert $groupSize byes (3)
            // Not the first iteration, and at the good frequency, and with $numByeTotal as limit
            if ($this->shouldInsertBye($frequency, $count, $byeCount, $numByeTotal)) { //
                for ($i = 0; $i < $groupSize; $i++) {
                    if ($byeCount < $numByeTotal) {
                        $newFighters->push($bye);
                        $byeCount++;
                    }
                }
            }
            $newFighters->push($fighter);
            $count++;
        }
        return $newFighters;
    }

    /**
     * @param $frequency
     * @param $groupSize
     * @param $count
     * @param $byeCount
     *
     * @return bool
     */
    private
    function shouldInsertBye($frequency, $count, $byeCount, $numByeTotal): bool
    {
        return $count != 0 && $count % $frequency == 0 && $byeCount < $numByeTotal;
    }


    /**
     * Method that fills fighters with Bye Groups at the end
     * @param $fighters
     * @param Collection $fighterGroups
     *
     * @return Collection
     */
    public function adjustFightersGroupWithByes($fighters, $fighterGroups): Collection
    {
        $tmpFighterGroups = clone $fighterGroups;
        $numBye = count($this->getByeGroup($fighters));

        // Get biggest competitor's group
        $max = $this->getMaxFightersByEntity($tmpFighterGroups);

        // We put them so that we can mix them up and they don't fight with another competitor of his entity.
        $fighters = $this->repart($fighterGroups, $max);

        if (!app()->runningUnitTests()) {
            $fighters = $fighters->shuffle();
        }
        // Insert byes to fill the tree.
        // Strategy: first, one group full, one group empty with byes, then groups of 2 fighters
        $fighters = $this->insertByes($fighters, $numBye);
        return $fighters;
    }

    /**
     * Get the biggest entity group.
     *
     * @param $userGroups
     *
     * @return int
     */
    private
    function getMaxFightersByEntity($userGroups): int
    {
        return $userGroups
            ->sortByDesc(function ($group) {
                return $group->count();
            })
            ->first()
            ->count();
    }

    /**
     * Repart BYE in the tree,.
     *
     * @param $fighterGroups
     * @param int $max
     *
     * @return Collection
     */
    private
    function repart($fighterGroups, $max)
    {
        $fighters = new Collection();
        for ($i = 0; $i < $max; $i++) {
            foreach ($fighterGroups as $fighterGroup) {
                $fighter = $fighterGroup->values()->get($i);
                if ($fighter != null) {
                    $fighters->push($fighter);
                }
            }
        }
        return $fighters;
    }
}
