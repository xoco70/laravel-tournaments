<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;

class PlayOffTreeGen extends TreeGen
{
    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param Championship $championship
     * @return Collection
     */
    protected function getByeGroup(Championship $championship, $fighters)
    {
        $fighterCount = $fighters->count();
        $preliminaryGroupSize = $championship->settings->preliminaryGroupSize;
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
        $numFightersEliminatory = $numFighters / $this->championship->getSettings()->preliminaryGroupSize * 2;
        // We calculate how much rounds we will have
        $numRounds = intval(log($numFightersEliminatory, 2));
        $this->pushGroups($numRounds, $numFightersEliminatory);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle
     * @param $round
     * @param $shuffle
     * @param $fightersByEntity
     * @return mixed
     */
    protected function chunkAndShuffle($round, $shuffle, $fightersByEntity)
    {
        if ($this->championship->hasPreliminary()) {
            $fightersGroup = $fightersByEntity->chunk($this->settings->preliminaryGroupSize);
            if ($shuffle) {
                $fightersGroup = $fightersGroup->shuffle();
            }
        } else { // Round Robin
            $fightersGroup = $fightersByEntity->chunk($fightersByEntity->count());
        }
        return $fightersGroup;
    }
}
