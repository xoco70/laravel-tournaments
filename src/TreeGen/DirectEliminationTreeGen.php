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
        $numRounds = $this->getNumRounds($numFightersElim);
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
        DirectEliminationFight::saveFights($this->championship);
    }

    /**
     *
     */
    public function generateNextRoundsFights()
    {
        $fightersCount = $this->championship->competitors->count() + $this->championship->teams->count();
        $maxRounds = $this->getNumRounds($fightersCount);
        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
            $fightsByRound = $this->championship->fightsByRound($numRound)->with('group.parent', 'group.children')->get();
            $this->updateParentFight($fightsByRound);
        }
    }


    /**
     * @param $fightsByRound
     */
    private function updateParentFight($fightsByRound)
    {
        foreach ($fightsByRound as $fight) {
            $parentGroup = $fight->group->parent;
            if ($parentGroup == null) break;
            $parentFight = $parentGroup->fights->get(0); //TODO This Might change when extending to Preliminary

            // IN this $fight, is c1 or c2 has the info?
            if ($this->championship->isDirectEliminationType()) {
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
                $group = $this->saveGroup(1, $matchNumber, $roundNumber, null);
                $this->syncGroup($group, $fighters);
            }
        }
    }

    /**
     * Return number of rounds for the tree based on fighter count
     * @param $numFighters
     * @return int
     */
    public function getNumRounds($numFighters)
    {
        return intval(log($numFighters, 2));
    }
}
