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
        $preliminaryGroupSize = 2;
        $treeSize = $this->getTreeSize($fighterCount, $preliminaryGroupSize);
        $byeCount = $treeSize - $fighterCount;

        return $this->createNullsGroup($byeCount);
    }


    /**
     * Create empty groups for direct Elimination Tree
     * @param $numFighters
     */
    public function pushEmptyGroupsToTree($numFighters) // KEEP
    {
        // We calculate how much rounds we will have
        $numFightersEliminatory = $numFighters;
        $numRounds = intval(log($numFightersEliminatory, 2));
        $this->pushGroups($numRounds, $numFightersEliminatory, $shuffle = 1);
    }
}
