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
    protected $tree;
    public $championship;
    public $settings;

    /**
     * @param Championship $championship
     * @param $groupBy
     */
    public function __construct(Championship $championship, $groupBy)
    {
        $this->championship = $championship;
        $this->groupBy = $groupBy;
        $this->settings = $championship->settings;
        $this->tree = new Collection();
    }

    /**
     * Generate tree groups for a championship.
     *
     * @throws TreeGenerationException
     */
    public function run()
    {
        $usersByArea = $this->getFightersByArea();
        $numFighters = sizeof($usersByArea->collapse());

        $this->pushEmptyGroupsToTree($numFighters);
        $this->generateGroupsForRound($usersByArea, $area = 1, $round = 1, $shuffle = 1);
    }

    /**
     * @param $userGroups
     *
     * @return int
     */
    private function getMaxFightersByEntity($userGroups): int
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
        return $fighterGroups;
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
     */
    public function generateGroupsForRound($usersByArea, $area, $round, $shuffle)
    {
        $previousRound = $this->getPreviousRound(1);
        dump($usersByArea);

        foreach ($usersByArea as $fightersByEntity) {
            // Chunking to make small round robin groups
            if ($this->championship->hasPreliminary()) {
                $fightersGroup = $fightersByEntity->chunk($this->settings->preliminaryGroupSize);
                if ($shuffle) $fightersGroup->shuffle();
            } elseif ($this->championship->isDirectEliminationType() || $round > 1) {
                $fightersGroup = $fightersByEntity->chunk(2);
                if ($shuffle) $fightersGroup->shuffle();
            } else { // Round Robin
                $fightersGroup = $fightersByEntity->chunk($fightersByEntity->count());
            }
            $order = sizeof($fightersGroup);
            // Before doing anything, check last group if numUser = 1
            foreach ($fightersGroup->reverse() as $value => $fighters) {
                $parent = $this->getParentGroup($round, null, $value + 1, $previousRound);
                $this->saveGroupAndSync($fighters, $area, $order, $round, $parent, $shuffle);
                $order--;
            }
            $area++;
        }
    }

    /**
     * @param $fighters
     * @param $area
     * @param $order
     * @param $round
     * @return FightersGroup
     */
    public function saveGroupAndSync($fighters, $area, $order, $round, $parent, $shuffle)
    {

        $fighters = $fighters->pluck('id');
        if ($shuffle) $fighters->shuffle();
        $group = $this->saveGroup($area, $order, $round, $parent);

        // Add all competitors to Pivot Table
        if ($this->championship->category->isTeam()) {
            $group->syncTeams($fighters);
        } else {
            $group->syncCompetitors($fighters);
        }

        return $group;
    }

    /**
     * Create empty groups for direct Elimination Tree
     * @param $numFighters
     */
    public function pushEmptyGroupsToTree($numFighters)
    {
        $numFightersEliminatory = $numFighters;
        // We check what will be the number of groups after the preliminaries
        if ($this->championship->hasPreliminary()) {
            $numFightersEliminatory = $numFighters / $this->championship->getSettings()->preliminaryGroupSize * 2;
        }
        // We calculate how much rounds we will have
        $numRounds = intval(ceil(log($numFightersEliminatory, 2)));
        $this->pushGroups($numRounds, $numFightersEliminatory, $shuffle = 1);
    }

    /**
     * @param $area
     * @param $order
     * @param $round
     * @param $parent
     * @return FightersGroup
     */
    private function saveGroup($area, $order, $round, $parent): FightersGroup
    {
        $group = new FightersGroup();
        $group->area = $area;
        $group->order = $order;
        $group->round = $round;
        $group->championship_id = $this->championship->id;
        if ($parent != null) {
            $group->parent_id = $parent->id;
        }
        $group->save();
        return $group;
    }

    private function createByeFighter()
    {
        return $this->championship->category->isTeam
            ? new Team()
            : new Competitor();
    }

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
     * @param $fighters
     * @param $fighterGroups
     * @return Collection
     */
    public function adjustFightersGroupWithByes($fighters, $fighterGroups): Collection
    {
        $tmpFighterGroups = clone $fighterGroups;

        $byeGroup = $this->getByeGroup($this->championship, $fighters);

        // Get biggest competitor's group
        $max = $this->getMaxFightersByEntity($tmpFighterGroups);

        // We reacommodate them so that we can mix them up and they don't fight with another competitor of his entity.

        $fighters = $this->repart($fighterGroups, $max);
        $fighters = $this->insertByes($fighters, $byeGroup);

        return $fighters;
    }

    /**
     * Get All Groups on previous round
     * @param $currentRound
     * @param $numRounds
     * @return Collection
     */
    private function getPreviousRound($currentRound, $numRounds = 0)
    {
        $previousRound = null;
        if ($currentRound != $numRounds) {
            $previousRound = $this->championship->groupsByRound($currentRound + 1)->get();
        }

        return $previousRound;
    }

    /**
     * Get the next group on the right ( parent ), final round being the ancestor
     * @param $roundNumber
     * @param $numRounds
     * @param $matchNumber
     * @param $previousRound
     * @return mixed
     */
    private function getParentGroup($roundNumber, $numRounds = 0, $matchNumber, $previousRound)
    {
        $parent = null;
        if ($roundNumber != $numRounds) { // Final, there is no more parent
            $parentIndex = intval(($matchNumber + 1) / 2);
            $parent = $previousRound->get($parentIndex - 1);
        }
        return $parent;
    }

    /**
     * Save Groups with their parent info
     * @param $numRounds
     * @param $numFightersEliminatory
     */
    private function pushGroups($numRounds, $numFightersEliminatory, $shuffle = true)
    {

        // From last round to first round
        for ($roundNumber = $numRounds; $roundNumber > 1; $roundNumber--) {
            $previousRound = $this->getPreviousRound($roundNumber, $numRounds);
            // From last match to first match
            for ($matchNumber = ($numFightersEliminatory / pow(2, $roundNumber)); $matchNumber > 0; $matchNumber--) {
                $parent = $this->getParentGroup($roundNumber, $numRounds, $matchNumber, $previousRound);
                $fighters = $this->createByeGroup(2);
                $this->saveGroupAndSync($fighters, $area = 1, $order = $matchNumber, $roundNumber, $parent, $shuffle);
            }
        }

    }

    /**
     * @return Collection
     * @throws TreeGenerationException
     */
    private function getFightersByArea()
    {
        // If previous trees already exists, delete all
        $this->championship->fightersGroups()->delete();
        $areas = $this->settings->fightingAreas;
        $fighters = $this->getFighters();

        if ($fighters->count() / $areas < ChampionshipSettings::MIN_COMPETITORS_BY_AREA) {
            throw new TreeGenerationException();
        }
        // Get Competitor's / Team list ordered by entities ( Federation, Assoc, Club, etc...)
        $fighterByEntity = $this->getFightersByEntity($fighters); // Chunk(1)
        $fightersWithBye = $this->adjustFightersGroupWithByes($fighters, $fighterByEntity);

        // Chunk user by areas
        $usersByArea = $fightersWithBye->chunk(count($fightersWithBye) / $areas);
        return $usersByArea;
    }
}
