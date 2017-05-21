<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Xoco70\KendoTournaments\Models\Championship;

class DirectEliminationTreeGen extends TreeGen
{

    /**
     * Calculate the Byes need to fill the Championship Tree.
     * @param Championship $championship
     * @param $fighters
     * @return Collection
     */
    protected function getByeGroup(Championship $championship, $fighters)
    {
        $fighterCount = $fighters->count();
        $treeSize = $this->getTreeSize($fighterCount, 2);
        $byeCount = $treeSize - $fighterCount;

        return $this->createNullsGroup($byeCount);
    }


    /**
     * Create empty groups for direct Elimination Tree
     * @param $numFightersElim
     */
    public function pushEmptyGroupsToTree($numFightersElim)
    {
        // We calculate how much rounds we will have
        $numRounds = intval(log($numFightersElim, 2));
        $this->pushGroups($numRounds, $numFightersElim);
    }

    /**
     * Chunk Fighters into groups for fighting, and optionnaly shuffle
     * @param $fightersByEntity
     * @return Collection|null
     */
    protected function chunkAndShuffle($round = null, $fightersByEntity)
    {
        $fightersGroup = null;

        $fightersGroup = $fightersByEntity->chunk(2);
        if (!App::runningUnitTests()) {
            $fightersGroup = $fightersGroup->shuffle();
        }
        return $fightersGroup;
    }
}
