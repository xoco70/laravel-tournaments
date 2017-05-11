<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\Championship;

class PlayOffTreeGen extends TreeGen
{
    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param Championship $championship
     * @return Collection
     */
    protected function getByeGroup(Championship $championship, $fighters)
    {
        $groupSizeDefault = 3;
        $fighterCount = $fighters->count();
        $preliminaryGroupSize = $championship->settings != null
            ? $championship->settings->preliminaryGroupSize
            : $groupSizeDefault;

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
        $this->pushGroups($numRounds, $numFightersEliminatory, $shuffle = 1);
    }
}
