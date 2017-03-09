<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Contracts\TreeGenerable;
use Xoco70\KendoTournaments\Exceptions\TreeGenerationException;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Team;

class TreeGen implements TreeGenerable
{
    protected $groupBy;
    public $championship;
    public $settings;

    /**
     * @param \Xoco70\KendoTournaments\Models\ChampionshipSettings $settings
     */
    public function __construct(Championship $championship, $groupBy, $settings)
    {
        $this->championship = $championship;
        $this->groupBy = $groupBy;
        $this->settings = $settings;
    }

    /**
     * Generate tree groups for a championship.
     *
     * @throws TreeGenerationException
     *
     * @return Collection
     */
    public function run()
    {
        // If previous trees already exist, delete all
        $this->championship->fightersGroups()->delete();
        $areas = $this->settings->fightingAreas;
        $fighters = $this->getFighters();

        if ($fighters->count() / $areas < ChampionshipSettings::MIN_COMPETITORS_BY_AREA) {
            throw new TreeGenerationException();
        }
        // Get Competitor's / Team list ordered by entities ( Federation, Assoc, Club, etc...)
        $fightersByEntity = $this->getFightersByEntity($fighters);

        // Chunk user by areas

        $usersByArea = $fightersByEntity->chunk(count($fightersByEntity) / $areas);

        $area = 1;

        // loop on areas
        $tree = $this->generateAllGroups($usersByArea, $area);
        return $tree;
    }

    /**
     * @param $userGroups
     *
     * @return int
     */
    private function getMaxCompetitorByEntity($userGroups): int
    {
        // Surely there is a Laravel function that does it ;)
        $max = 0;
        foreach ($userGroups as $userGroup) {
            if (count($userGroup) > $max) {
                $max = count($userGroup);
            }
        }

        return $max;
    }

    /**
     * Get Competitor's list ordered by entities
     * Countries for Internation Tournament, State for a National Tournament, etc.
     *
     * @return Collection
     */
    private function getFightersByEntity($fighters): Collection
    {
        // Right now, we are treating users and teams as equals.
        // It doesn't matter right now, because we only need name attribute which is common to both models

        // $this->groupBy contains federation_id, association_id, club_id, etc.
        if (($this->groupBy) != null) {
            $fighterGroups = $fighters->groupBy($this->groupBy); // Collection of Collection
        } else {
            $fighterGroups = $fighters->chunk(1); // Collection of Collection
        }

        $tmpFighterGroups = clone $fighterGroups;

        $byeGroup = $this->getByeGroup($this->championship, $fighters);

        // Get biggest competitor's group
        $max = $this->getMaxCompetitorByEntity($tmpFighterGroups);

        // We reacommodate them so that we can mix them up and they don't fight with another competitor of his entity.

        $competitors = $this->repart($fighterGroups, $max);

        $competitors = $this->insertByes($competitors, $byeGroup);

        return $competitors;
    }

    /**
     * Calculate the Byes need to fill the Championship Tree.
     *
     * @param Championship $championship
     *
     * @return Collection
     */
    private function getByeGroup(Championship $championship, $fighters)
    {
        $groupSizeDefault = 3;
        $preliminaryGroupSize = 2;

        $fighterCount = $fighters->count();

        if ($championship->hasPreliminary()) {
            $preliminaryGroupSize = $championship->settings != null
                ? $championship->settings->preliminaryGroupSize
                : $groupSizeDefault;
        } elseif ($championship->isDirectEliminationType()) {
            $preliminaryGroupSize = 2;
        } else {
            // No Preliminary and No Direct Elimination --> Round Robin
            // Should Have no tree
        }
        $treeSize = $this->getTreeSize($fighterCount, $preliminaryGroupSize);

        $byeCount = $treeSize - $fighterCount;

        return $this->createNullsGroup($byeCount, $championship->category->isTeam);
    }

    /**
     * @param $fighterCount
     *
     * @return int
     */
    private function getTreeSize($fighterCount, $groupSize)
    {
        $square = collect([1, 2, 4, 8, 16, 32, 64]);
        $squareMultiplied = $square->map(function ($item, $key) use ($groupSize) {
            return $item * $groupSize;
        });

        foreach ($squareMultiplied as $limit) {
            if ($fighterCount <= $limit) {
                return $limit;
            }
        }

        return 64 * $groupSize;
    }

    /**
     * @param $byeCount
     *
     * @return Collection
     */
    private function createNullsGroup($byeCount, $isTeam): Collection
    {
        $isTeam
            ? $null = new Team()
            : $null = new Competitor();

        $byeGroup = new Collection();
        for ($i = 0; $i < $byeCount; $i++) {
            $byeGroup->push($null);
        }

        return $byeGroup;
    }

    /**
     * @param $fighterGroups
     * @param int $max
     *
     * @return Collection
     */
    private function repart($fighterGroups, $max)
    {
        $fighters = new Collection();
        for ($i = 0; $i < $max; $i++) {
            foreach ($fighterGroups as $fighterGroup) {
                $fighter = $fighterGroup->values()->get($i);
                if ($fighter != null) {
                    $fighters->push($fighter);
                }
            }
        }

        return $fighters;
    }

    /**
     * Insert byes in an homogen way.
     *
     * @param Collection $fighters
     * @param Collection $byeGroup
     *
     * @return Collection
     */
    private function insertByes(Collection $fighters, Collection $byeGroup)
    {
        $bye = count($byeGroup) > 0 ? $byeGroup[0] : [];
        $sizeFighters = count($fighters);
        $sizeGroupBy = count($byeGroup);

        $frequency = $sizeGroupBy != 0
            ? (int)floor($sizeFighters / $sizeGroupBy)
            : -1;

        // Create Copy of $competitors
        $newFighters = new Collection();
        $i = 0;
        $byeCount = 0;
        foreach ($fighters as $fighter) {
            if ($frequency != -1 && $i % $frequency == 0 && $byeCount < $sizeGroupBy) {
                $newFighters->push($bye);
                $byeCount++;
            }
            $newFighters->push($fighter);
            $i++;
        }

        return $newFighters;
    }

    private function getFighters()
    {
        $this->championship->category->isTeam()
            ? $fighters = $this->championship->teams
            : $fighters = $this->championship->competitors;

        return $fighters;
    }

    /**
     * @param $usersByArea
     * @param $area
     *
     * @return Collection
     */
    public function generateAllGroups($usersByArea, $area)
    {
        $groups = new Collection();
        foreach ($usersByArea as $fightersByEntity) {
            // Chunking to make small round robin groups
            if ($this->championship->hasPreliminary()) {
                $fightersGroup = $fightersByEntity->chunk($this->settings->preliminaryGroupSize)->shuffle();
            } elseif ($this->championship->isDirectEliminationType()) {
                $fightersGroup = $fightersByEntity->chunk(2)->shuffle();
            } else { // Round Robin
                $fightersGroup = $fightersByEntity->chunk($fightersByEntity->count());
            }

            $order = 1;

            // Before doing anything, check last group if numUser = 1
            foreach ($fightersGroup as $fighters) {
                $group = $this->saveGroup($area, $fighters, $order, $groups);
                $groups->push($group);
                $order++;
            }
            $area++;
        }

        return $groups;
    }

    /**
     * @param $area
     * @param $fighters
     * @param $order
     *
     * @return FightersGroup
     */
    public function saveGroup($area, $fighters, $order)
    {
        $fighters = $fighters->pluck('id')->shuffle();

        $group = new FightersGroup();
        $group->area = $area;
        $group->order = $order;
        $group->championship_id = $this->championship->id;

        $group->save();

        // Add all competitors to Pivot Table
        if ($this->championship->category->isTeam()) {
            $group->syncTeams($fighters);
        } else {
            $group->syncCompetitors($fighters);
        }

        return $group;
    }
}
