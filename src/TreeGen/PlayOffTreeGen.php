<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Exceptions\TreeGenerationException;
use Xoco70\LaravelTournaments\Models\SingleEliminationFight;

abstract class PlayOffTreeGen extends TreeGen
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
        $treeSize = $this->getTreeSize($fighterCount, 2);
        $byeCount = $treeSize - $fighterCount;

        return $this->createByeGroup($byeCount);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle.
     *
     * @param $fightersByEntity
     *
     * @return mixed
     */
    protected function chunk(Collection $fightersByEntity)
    {
        if ($this->championship->hasPreliminary()) {
            $fightersGroup = $fightersByEntity->chunk($this->settings->preliminaryGroupSize);
            return $fightersGroup;
        }

        return $fightersByEntity->chunk($fightersByEntity->count());
    }

    /**
     * Generate First Round Fights.
     */
    public function generateFights()
    {
        parent::destroyPreviousFights($this->championship);
        SingleEliminationFight::saveFights($this->championship);
    }

    /**
     * Save Groups with their parent info.
     *
     * @param int $numRounds
     * @param $numFightersElim
     */
    protected function pushGroups($numRounds, $numFightersElim)
    {
        for ($roundNumber = 2; $roundNumber <= $numRounds; $roundNumber++) {
            // From last match to first match
            $maxMatches = ($numFightersElim / pow(2, $roundNumber));
            for ($matchNumber = 1; $matchNumber <= $maxMatches; $matchNumber++) {
                $fighters = $this->createByeGroup(2);
                $group = $this->saveGroup($matchNumber, $roundNumber, null);
                $this->syncGroup($group, $fighters);
            }
        }
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
        return intval(log($numFighters, 2));
    }

    /**
     * @throws TreeGenerationException
     */
    protected function generateAllTrees()
    {
        // TODO This is limiting Playoff only have 1 area
        $fighters = $this->championship->fighters;
        // This means that when playoff, we only generate 1 group
        // Could be better, for now it is ok
        $fighterType = $this->settings->isTeam
            ? trans_choice('laravel-tournaments::core.team', 2)
            : trans_choice('laravel-tournaments::core.competitor', 2);

        if (count($fighters) <= 1) {
            throw new TreeGenerationException(trans('laravel-tournaments::core.min_competitor_required', [
                'number'       => $this->settings->preliminaryGroupSize,
                'fighter_type' => $fighterType,
            ]), 422);
        }
        $this->generateGroupsForRound($fighters, 1);
    }

    protected function generateGroupsForRound(Collection $fightersByArea, $round)
    {
        $fightersId = $fightersByArea->pluck('id');
        $group = $this->saveGroup($round, $round, null);
        $this->syncGroup($group, $fightersId);
    }
}
