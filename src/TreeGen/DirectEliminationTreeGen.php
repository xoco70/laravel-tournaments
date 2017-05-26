<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\DirectEliminationFight;
use Xoco70\KendoTournaments\Models\Fight;

class DirectEliminationTreeGen extends TreeGen
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


    /**
     * Generate First Round Fights
     */
    public function generateFights()
    {
        parent::destroyPreviousFights($this->championship);
        // Very specific case to common case : Preliminary with 3 fighters
        DirectEliminationFight::saveFights($this->championship);
    }
    /**
     *
     */
    public function generateNextRoundsFights()
    {
        $fightersCount = $this->championship->competitors->count() + $this->championship->teams->count();
        $maxRounds = intval(ceil(log($fightersCount, 2)));
        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
            $fightsByRound = $this->championship->fightsByRound($numRound)->with('group.parent', 'group.children')->get();
            $this->updateParentFight($this->championship, $fightsByRound);
        }
    }


    /**
     * @param Championship $championship
     * @param $fightsByRound
     */
    private function updateParentFight(Championship $championship, $fightsByRound)
    {
        foreach ($fightsByRound as $fight) {
            $parentGroup = $fight->group->parent;
            if ($parentGroup == null) break;
            $parentFight = $parentGroup->fights->get(0); //TODO This Might change when extending to Preliminary

            // IN this $fight, is c1 or c2 has the info?
            if ($championship->isDirectEliminationType()) {
                // determine whether c1 or c2 must be updated
                $this->chooseAndUpdateParentFight($fight, $parentFight);
            }
        }
    }

    /**
     * @param $fight
     * @param $parentFight
     */
    private function chooseAndUpdateParentFight($fight, $parentFight)
    {
        $fighterToUpdate = $fight->getParentFighterToUpdate();
        $valueToUpdate = $fight->getValueToUpdate();
        // we need to know if the child has empty fighters, is this BYE or undetermined
        if ($fight->hasDeterminedParent() && $valueToUpdate != null) {
            $parentFight->$fighterToUpdate = $fight->$valueToUpdate;
            $parentFight->save();
        }
    }


    /**
     * Returns the parent field that need to be updated
     * @return null|string
     */
    public function getParentFighterToUpdate()
    {
        $childrenGroup = $this->group->parent->children;
        foreach ($childrenGroup as $key => $children) {
            $childFight = $children->fights->get(0);
            if ($childFight->id == $this->id) {
                if ($key % 2 == 0) {
                    return "c1";
                }
                if ($key % 2 == 1) {
                    return "c2";
                }
            }
        }
        return null;
    }

    /**
     * In the original fight ( child ) return the field that contains data to copy to parent
     * @return null|string
     */
    public function getValueToUpdate()
    {
        if ($this->c1 != null && $this->c2 != null) {
            return null;
        }
        if ($this->c1 != null) {
            return "c1";
        }
        if ($this->c2 != null) {
            return "c2";
        }
        return null;
    }

    /**
     * Check if we are able to fill the parent fight or not
     * If one of the children has c1 x c2, then we must wait to fill parent
     *
     * @return bool
     */
    public function hasDeterminedParent()
    {
        if ($this->group->has2Fighters()) return true;
        foreach ($this->group->children as $child) {
            $fight = $child->fights->get(0);
            if ($fight->has2Fighters()) return false;
        }
        return true;
    }
}
