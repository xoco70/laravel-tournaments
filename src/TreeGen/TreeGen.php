<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Contracts\TreeGenerable;
use Xoco70\LaravelTournaments\Exceptions\TreeGenerationException;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Fight;
use Xoco70\LaravelTournaments\Models\FightersGroup;

abstract class TreeGen implements TreeGenerable
{
    protected $groupBy;
    protected $tree;
    public $championship;
    public $settings;
    protected $numFighters;

    abstract protected function generateAllTrees();

    abstract protected function generateFights();

    abstract protected function addFighterToGroup(FightersGroup $group, $fighter, $fighterToUpdate);

    abstract protected function getByeGroup($fighters);

    abstract protected function getNumRounds($fightersCount);

    abstract protected function chunk(Collection $fightersByEntity);

    abstract protected function syncGroup(FightersGroup $group, $fighters);

    abstract protected function generateGroupsForRound(Collection $fightersByArea, $round);

    /**
     * @param Championship $championship
     * @param $groupBy
     */
    public function __construct(Championship $championship, $groupBy)
    {
        $this->championship = $championship;
        $this->groupBy = $groupBy;
        $this->settings = $championship->getSettings();
        $this->tree = new Collection();
    }

    /**
     * Generate tree groups for a championship.
     *
     * @throws TreeGenerationException
     */
    public function run()
    {
        $this->championship->fightersGroups()->delete();
        $this->generateAllTrees();
        $this->generateAllFights();
    }

    /**
     * Get Competitor's list ordered by entities
     * Countries for Internation Tournament, State for a National Tournament, etc.
     *
     * @param $fighters
     *
     * @return Collection
     */
    private function getFightersByEntity($fighters): Collection
    {
        // Right now, we are treating users and teams as equals.
        // It doesn't matter right now, because we only need name attribute which is common to both models

        // $this->groupBy contains federation_id, association_id, club_id, etc.
        if (($this->groupBy) != null) {
            return $fighters->groupBy($this->groupBy); // Collection of Collection
        }

        return $fighters->chunk(1); // Collection of Collection
    }

    /**
     * Get the size the first round will have.
     *
     * @param $fighterCount
     * @param $groupSize
     *
     * @return int
     */
    protected function getTreeSize($fighterCount, $groupSize)
    {
        $squareMultiplied = collect([1, 2, 4, 8, 16, 32, 64])
            ->map(function ($item) use ($groupSize) {
                return $item * $groupSize;
            }); // [4, 8, 16, 32, 64,...]

        foreach ($squareMultiplied as $limit) {
            if ($fighterCount <= $limit) {
                $treeSize = $limit;
                $numAreas = $this->settings->fightingAreas;
                $fighterCountPerArea = $treeSize / $numAreas;
                if ($fighterCountPerArea < $groupSize) {
                    $treeSize = $treeSize * $numAreas;
                }

                return $treeSize;
            }
        }

        return 64 * $groupSize;
    }

    /**
     * @param $order
     * @param $round
     * @param $parent
     *
     * @return FightersGroup
     */
    protected function saveGroup($order, $round, $parent): FightersGroup
    {
        $group = new FightersGroup();
        $this->championship->isSingleEliminationType()
            ? $group->area = $this->getNumArea($round, $order)
            : $group->area = 1; // Area limited to 1 in playoff

        $group->order = $order;
        $group->round = $round;
        $group->championship_id = $this->championship->id;
        if ($parent != null) {
            $group->parent_id = $parent->id;
        }
        $group->save();

        return $group;
    }

    /**
     * @param int $groupSize
     *
     * @return Collection
     */
    public function createByeGroup($groupSize): Collection
    {
        $byeFighter = $this->createByeFighter();
        $group = new Collection();
        for ($i = 0; $i < $groupSize; $i++) {
            $group->push($byeFighter);
        }

        return $group;
    }

    /**
     * Get All Groups on previous round.
     *
     * @param $currentRound
     *
     * @return Collection
     */
    private function getPreviousRound($currentRound)
    {
        $previousRound = $this->championship->groupsByRound($currentRound + 1)->get();

        return $previousRound;
    }

    /**
     * Get the next group on the right ( parent ), final round being the ancestor.
     *
     * @param $matchNumber
     * @param Collection $previousRound
     *
     * @return mixed
     */
    private function getParentGroup($matchNumber, $previousRound)
    {
        $parentIndex = intval(($matchNumber + 1) / 2);
        $parent = $previousRound->get($parentIndex - 1);

        return $parent;
    }

    /**
     * Group Fighters by area.
     * Here is where we fill with empty fighters.
     *
     * @throws TreeGenerationException
     *
     * @return Collection
     */
    protected function getFightersByArea()
    {
        $areas = $this->settings->fightingAreas;
        $fighters = $this->getFighters();   // Get Competitor or Team Objects
        $fighterByEntity = $this->getFightersByEntity($fighters);   // Chunk it by entities (Fede, Assoc, Club,...)
        $fightersWithBye = $this->adjustFightersGroupWithByes($fighters, $fighterByEntity);     // Fill with Byes
        return $fightersWithBye->chunk(count($fightersWithBye) / $areas);   // Chunk user by areas
    }

    /**
     * Logically build the tree ( attach a parent to every child for nestedSet Navigation ).
     *
     * @param $numFighters
     */
    protected function addParentToChildren($numFighters)
    {
        $numRounds = $this->getNumRounds($numFighters);
        $groupsDesc = $this->championship
            ->fightersGroups()
            ->where('round', '<', $numRounds)
            ->orderByDesc('id')->get();

        $groupsDescByRound = $groupsDesc->groupBy('round');

        foreach ($groupsDescByRound as $round => $groups) {
            $previousRound = $this->getPreviousRound($round);
            foreach ($groups->reverse()->values() as $matchNumber => $group) {
                $parent = $this->getParentGroup($matchNumber + 1, $previousRound);
                $group->parent_id = $parent->id;
                $group->save();
            }
        }
    }

    /**
     * Destroy Previous Fights for demo.
     */
    protected function destroyPreviousFights()
    {
        // Delete previous fight for this championship
        $arrGroupsId = $this->championship->fightersGroups()->get()->pluck('id');
        if (count($arrGroupsId) > 0) {
            Fight::destroy($arrGroupsId);
        }
    }

    /**
     * Generate Fights for next rounds.
     */
    public function generateNextRoundsFights()
    {
        $fightersCount = $this->championship->competitors->count() + $this->championship->teams->count();
        $maxRounds = $this->getNumRounds($fightersCount);
        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
            $groupsByRound = $this->championship->fightersGroups()->where('round', $numRound)->with('parent', 'children')->get();
            $this->updateParentFight($groupsByRound); // should be groupsByRound
        }
    }

    /**
     * @param $groupsByRound
     */
    protected function updateParentFight($groupsByRound)
    {
        foreach ($groupsByRound as $keyGroup => $group) {
            $parentGroup = $group->parent;
            if ($parentGroup == null) {
                break;
            }
            $parentFight = $parentGroup->fights->get(0);

            // determine whether c1 or c2 must be updated
            $this->chooseAndUpdateParentFight($keyGroup, $group, $parentFight);
        }
    }

    /**
     * @param $group
     * @param $parentFight
     */
    protected function chooseAndUpdateParentFight($keyGroup, FightersGroup $group, Fight $parentFight)
    {
        // we need to know if the child has empty fighters, is this BYE or undetermined
        if ($group->hasDeterminedParent()) {
            $valueToUpdate = $group->getValueToUpdate(); // This should be OK
            if ($valueToUpdate != null) {
                $fighterToUpdate = $group->getParentFighterToUpdate($keyGroup);
                $parentFight->$fighterToUpdate = $valueToUpdate;
                $parentFight->save();
                // Add fighter to pivot table
                $parentGroup = $parentFight->group;

                $fighter = $this->getFighter($valueToUpdate);
                $this->addFighterToGroup($parentGroup, $fighter, $fighterToUpdate);
            }
        }
    }

    /**
     * Calculate the area of the group ( group is still not created ).
     *
     * @param $round
     * @param $order
     *
     * @return int
     */
    protected function getNumArea($round, $order)
    {
        $totalAreas = $this->settings->fightingAreas;
        $numFighters = $this->championship->fighters->count(); // 4
        $numGroups = $this->getTreeSize($numFighters, $this->championship->getGroupSize()) / $this->championship->getGroupSize(); // 1 -> 1

        $areaSize = $numGroups / ($totalAreas * pow(2, $round - 1));

        $numArea = intval(ceil($order / $areaSize)); // if round == 4, and second match 2/2 = 1 BAD
        return $numArea;
    }

    protected function generateAllFights()
    {
        $this->generateFights(); // Abstract
        $this->generateNextRoundsFights();
        Fight::generateFightsId($this->championship);
    }
}
