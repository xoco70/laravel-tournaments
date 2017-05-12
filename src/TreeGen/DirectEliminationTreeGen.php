<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\Championship;

class DirectEliminationTreeGen extends TreeGen
{

    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param Championship $championship
     * @return Collection
     */
    protected function getByeGroup(Championship $championship, $fighters)
    {
        $fighterCount = $fighters->count();
        $treeSize = $this->getTreeSize($fighterCount, $preliminaryGroupSize = 2);
        $byeCount = $treeSize - $fighterCount;

        return $this->createNullsGroup($byeCount);
    }


    /**
     * Create empty groups for direct Elimination Tree
     * @param $numFightersEliminatory
     */
    public function pushEmptyGroupsToTree($numFightersEliminatory)
    {
        // We calculate how much rounds we will have
        $numRounds = intval(log($numFightersEliminatory, 2));
        $this->pushGroups($numRounds, $numFightersEliminatory);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle
     * @param $round
     * @param $shuffle
     * @param $fightersByEntity
     * @return Collection|null
     */
    protected function chunkAndShuffle($round, $shuffle, $fightersByEntity)
    {
        $fightersGroup = null;

        $fightersGroup = $fightersByEntity->chunk(2);
        if ($shuffle) {
            $fightersGroup = $fightersGroup->shuffle();
        }
        return $fightersGroup;
    }
}
