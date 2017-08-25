<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\DirectEliminationFight;


abstract class PlayOffTreeGen extends TreeGen
{
    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param $fighters
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
     * Create empty groups for PlayOff Round
     * @param $numFighters
     */
    protected function pushEmptyGroupsToTree($numFighters)
    {
        //TODO CHANGE HERE TOO
        $numFightersElim = $numFighters / $this->championship->getSettings()->preliminaryGroupSize * 2;
        // We calculate how much rounds we will have
        $numRounds = intval(log($numFightersElim, 2)); // 3 rounds, but begining from round 2 ( ie => 4)
        $this->pushGroups($numRounds, $numFightersElim);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle
     * @param $fightersByEntity
     * @return mixed
     */
    protected function chunkAndShuffle(Collection $fightersByEntity)
    {
        if ($this->championship->hasPreliminary()) {

            $fightersGroup = $fightersByEntity->chunk($this->settings->preliminaryGroupSize);
            if (!app()->runningUnitTests()) {
                $fightersGroup = $fightersGroup->shuffle();
            }
            return $fightersGroup;
        }
        return $fightersByEntity->chunk($fightersByEntity->count());
    }


    /**
     * Generate First Round Fights
     */
    public function generateFights()
    {
        parent::destroyPreviousFights($this->championship);
        DirectEliminationFight::saveFights($this->championship);
    }


    /**
     * Save Groups with their parent info
     * @param integer $numRounds
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
     * Return number of rounds for the tree based on fighter count
     * @param $numFighters
     * @return int
     */
    protected function getNumRounds($numFighters)
    {
        return intval(log($numFighters, 2));
    }

}
