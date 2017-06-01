<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Fight;
use Xoco70\KendoTournaments\Models\PreliminaryFight;

class PlayOffTreeGen extends TreeGen
{


    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param Championship $championship
     * @param $fighters
     * @return Collection
     */
    protected function getByeGroup($fighters)
    {
        $fighterCount = $fighters->count();
        $preliminaryGroupSize = $this->championship->getSettings()->preliminaryGroupSize;
        $treeSize = $this->getTreeSize($fighterCount, $preliminaryGroupSize);
        $byeCount = $treeSize - $fighterCount;

        return $this->createNullsGroup($byeCount);
    }


    /**
     * Create empty groups for direct Elimination Tree
     * @param $numFighters
     */
    public function pushEmptyGroupsToTree($numFighters)
    {
        $numFightersElim = $numFighters / $this->championship->getSettings()->preliminaryGroupSize * 2;
        // We calculate how much rounds we will have
        $numRounds = intval(log($numFightersElim, 2));
        $this->pushGroups($numRounds, $numFightersElim);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle
     * @param $round
     * @param $fightersByEntity
     * @return mixed
     */
    protected function chunkAndShuffle($round, $fightersByEntity)
    {
        if ($this->championship->hasPreliminary()) {
            $fightersGroup = $fightersByEntity->chunk($this->settings->preliminaryGroupSize);
            if (!App::runningUnitTests()) {
                $fightersGroup = $fightersGroup->shuffle();
            }
        } else { // Round Robin
            $fightersGroup = $fightersByEntity->chunk($fightersByEntity->count());
        }
        return $fightersGroup;
    }

    /**
     * Generate First Round Fights
     * @param Championship $championship
     */
    public function generateFights()
    {
        $settings = $this->championship->getSettings();
        parent::destroyPreviousFights();
        // Very specific case to common case : Preliminary with 3 fighters
        if ($settings->preliminaryGroupSize == 3) {
            for ($numGroup = 1; $numGroup <= $settings->preliminaryGroupSize; $numGroup++) {
                PreliminaryFight::saveFights($this->championship->fightersGroups()->get(), $numGroup);
            }
        }
    }


    public function generateNextRoundsFights()
    {
//        $championship = $this->championship->withCount('teams', 'competitors')->first();
//        $fightersCount = $championship->competitors_count + $championship->teams_count;
//        $maxRounds = intval(ceil(log($fightersCount, 2)));
//        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
//            $fightsByRound = $championship->fightsByRound($numRound)->with('group.parent', 'group.children')->get();
//            $this->updateParentFight($championship, $fightsByRound);
//        }
    }

}
