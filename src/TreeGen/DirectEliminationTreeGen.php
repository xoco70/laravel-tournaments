<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\DirectEliminationFight;
use Xoco70\LaravelTournaments\Models\PreliminaryFight;

abstract class DirectEliminationTreeGen extends TreeGen
{

    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param $fighters
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
     * Save Groups with their parent info
     * @param integer $numRounds
     * @param integer $numFighters
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
    }

    /**
     * Create empty groups for direct Elimination Tree
     * @param $numFighters
     */
    protected function pushEmptyGroupsToTree($numFighters)
    {
        if ($this->championship->hasPreliminary()) {
            $numFightersElim = $numFighters / $this->championship->getSettings()->preliminaryGroupSize * 2;
            // We calculate how much rounds we will have
            $numRounds = intval(log($numFightersElim, 2)); // 3 rounds, but begining from round 2 ( ie => 4)
            return $this->pushGroups($numRounds, $numFightersElim);
        }
        // We calculate how much rounds we will have
        $numRounds = $this->getNumRounds($numFighters);
        return $this->pushGroups($numRounds, $numFighters);

    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle
     * @param $fightersByEntity
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
     * Generate First Round Fights
     */
    protected function generateFights()
    {
        //  First Round Fights
        $settings = $this->championship->getSettings();
//        dd("ok");
//        parent::destroyPreviousFights();
        $groups = $this->championship->groupsByRound(1)->get();
        $initialRound = 1;
        // Very specific case to common case : Preliminary with 3 fighters
        if ($this->championship->hasPreliminary() && $settings->preliminaryGroupSize == 3) {
            // First we make all first fights of all groups
            // Then we make all second fights of all groups
            // Then we make all third fights of all groups
            for ($numFight = 1; $numFight <= $settings->preliminaryGroupSize; $numFight++) {
                $fight = new PreliminaryFight;
                $fight->saveFights($groups, $numFight);
            }
            $initialRound++;
        }
        // Save Next rounds
        $fight = new DirectEliminationFight;
        $fight->saveFights($this->championship, $initialRound);
    }


    /**
     * Return number of rounds for the tree based on fighter count
     * @param $numFighters
     * @return int
     */
    protected function getNumRounds($numFighters)
    {
        return intval(log($numFighters / $this->firstRoundGroupSize() * 2, 2));
    }

    private function firstRoundGroupSize()
    {
        return $this->championship->hasPreliminary()
            ? $this->championship->getSettings()->preliminaryGroupSize
            : 2;
    }
}
