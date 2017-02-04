<?php


namespace App\TreeGen;


use App\Championship;
use App\ChampionshipSettings;
use App\Contracts\TreeGenerable;
use App\Exceptions\TreeGenerationException;
use App\Team;
use App\Tree;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class PreliminaryTreeGen implements TreeGenerable
{

    protected $groupBy;
    public $championship, $error;


    public function __construct(Championship $championship, $groupBy)
    {
        $this->championship = $championship;
        $this->groupBy = $groupBy;
//        $this->error = null;
    }

    /**
     * Generate tree groups for a championship
     * @return Collection
     * @throws TreeGenerationException
     */
    public function run()
    {
        // If previous trees already exist, delete all
        $this->championship->tree()->delete();

        // Get Settings
        $trees = new Collection();
        $settings = $this->championship->settings ??  new ChampionshipSettings(config('options.default_settings'));

        // Get Areas
        $areas = $settings->fightingAreas;

        $this->championship->category->isTeam()
            ? $fighters = $this->championship->teams
            : $fighters = $this->championship->users;

        if ($fighters->count() / $areas < config('constants.MIN_COMPETITORS_X_AREA')) {
            throw new TreeGenerationException(trans('msg.min_competitor_required', ['number' => Config::get('constants.MIN_COMPETITORS_X_AREA')]));

        }

        // Get Competitor's / Team list ordered by entities ( Federation, Assoc, Club, etc...)
        $users = $this->getUsersByEntity();

        // Chunk user by areas

        $usersByArea = $users->chunk(sizeof($users) / $areas);

        $area = 1;

        // loop on areas
        foreach ($usersByArea as $users) {

            // Chunking to make small round robin groups
                if ($this->championship->hasPreliminary()) {
                $roundRobinGroups = $users->chunk($settings->preliminaryGroupSize)->shuffle();

            } else if ($this->championship->isDirectEliminationType()) {
                $roundRobinGroups = $users->chunk(2)->shuffle();
            }else{
                $roundRobinGroups = new Collection();

                // Not so good, Round Robin has no trees
                $pt = new Tree;
                $pt->area = $area;
                $pt->championship_id = $this->championship->id;
                if ($this->championship->category->isTeam()) {
                    $pt->isTeam = 1;
                }
                $pt->save();
                $trees->push($pt);
            }

            $order = 1;

            // Before doing anything, check last group if numUser = 1
            foreach ($roundRobinGroups as $robinGroup) {

                $robinGroup = $robinGroup->shuffle()->values();

                $pt = new Tree;
                $pt->area = $area;
                $pt->order = $order;
                if ($this->championship->category->isTeam()) {
                    $pt->isTeam = 1;
                }
                $pt->championship_id = $this->championship->id;


                $c1 = $robinGroup->get(0);
                $c2 = $robinGroup->get(1);
                $c3 = $robinGroup->get(2);
                $c4 = $robinGroup->get(3);
                $c5 = $robinGroup->get(4);

                if (isset($c1)) $pt->c1 = $c1->id;
                if (isset($c2)) $pt->c2 = $c2->id;
                if (isset($c3)) $pt->c3 = $c3->id;
                if (isset($c4)) $pt->c4 = $c4->id;
                if (isset($c5)) $pt->c5 = $c5->id;
                $pt->save();
                $trees->push($pt);
                $order++;
            }
            $area++;
        }

        return $trees;
    }

    /**
     * @param $userGroups
     * @return int
     */
    private function getMaxCompetitorByEntity($userGroups): int
    {
        // Surely there is a Laravel function that does it ;)
        $max = 0;
        foreach ($userGroups as $userGroup) {
            if (sizeof($userGroup) > $max) {
                $max = sizeof($userGroup);
            }

        }
        return $max;
    }

    /**
     * Get Competitor's list ordered by entities
     * Countries for Internation Tournament, State for a National Tournament, etc
     * @return Collection
     */
    private function getUsersByEntity(): Collection
    {
//        $competitors = new Collection();

        // Right now, we are treating users and teams as equals.
        // It doesn't matter right now, because we only need name attribute which is common to both models

        // $this->groupBy contains federation_id, association_id, club_id, etc.
        if ($this->championship->category->isTeam()) {
            if (($this->groupBy) != null) {
                $userGroups = $this->championship->teams->groupBy($this->groupBy); // Collection of Collection
            } else {
                $userGroups = $this->championship->teams->chunk(1); // Collection of Collection
            }
        } else {
            if (($this->groupBy) != null) {
                $userGroups = $this->championship->users->groupBy($this->groupBy); // Collection of Collection
            } else {
                $userGroups = $this->championship->users->chunk(1); // Collection of Collection
            }
        }

        $tmpUserGroups = clone $userGroups;

        $byeGroup = $this->getByeGroup($this->championship);


        // Get biggest competitor's group
        $max = $this->getMaxCompetitorByEntity($tmpUserGroups);

        // We reacommodate them so that we can mix them up and they don't fight with another competitor of his entity.

        $competitors = $this->repart($userGroups, $max);

        $competitors = $this->insertByes($competitors, $byeGroup);

        return $competitors;

    }

    /**
     * Calculate the Byes need to fill the Championship Tree
     * @param Championship $championship
     * @return Collection
     */
    private function getByeGroup(Championship $championship)
    {
        $groupSizeDefault = 3;
        $preliminaryGroupSize = 2;

        if ($championship->category->isTeam) {
            $userCount = $championship->teams->count();
        } else {
            $userCount = $championship->users->count();
        }

        if ($championship->hasPreliminary()) {
            $preliminaryGroupSize = $championship->settings != null
                ? $championship->settings->preliminaryGroupSize
                : $groupSizeDefault;
        } else if ($championship->isDirectEliminationType()) {
            $preliminaryGroupSize = 2;

        } else {
            // No Preliminary and No Direct Elimination --> Round Robin
            // Should Have no tree
        }
        $treeSize = $this->getTreeSize($userCount, $preliminaryGroupSize);

        $byeCount = $treeSize - $userCount;
        return $this->createNullsGroup($byeCount, $championship->category->isTeam);
    }

    /**
     * @param $userCount
     * @return integer
     */
    private function getTreeSize($userCount, $groupSize)
    {
        $square = collect([1, 2, 4, 8, 16, 32, 64]);
        $squareMultiplied = $square->map(function ($item, $key) use ($groupSize) {
            return $item * $groupSize;
        });


        foreach ($squareMultiplied as $limit) {
            if ($userCount <= $limit) {

                return $limit;
            }
        }
        return 64 * $groupSize;

    }

    /**
     * @param $byeCount
     * @return Collection
     */
    private function createNullsGroup($byeCount, $isTeam): Collection
    {
        $isTeam
            ? $null = new Team()
            : $null = new User();

        $byeGroup = new Collection();
        for ($i = 0; $i < $byeCount; $i++) {
            $byeGroup->push($null);
        }
        return $byeGroup;
    }

    /**
     * @param $userGroups
     * @param $max
     * @return Collection
     */
    private function repart($userGroups, $max)
    {
        $competitors = new Collection;
        for ($i = 0; $i < $max; $i++) {
            foreach ($userGroups as $userGroup) {
                $competitor = $userGroup->values()->get($i);
                if ($competitor != null) {
                    $competitors->push($competitor);
                }
            }
        }
        return $competitors;
    }

    /**
     * Insert byes in an homogen way
     * @param Collection $competitors
     * @param Collection $byeGroup
     * @return Collection
     */
    private function insertByes(Collection $competitors, Collection $byeGroup)
    {
        $bye = sizeof($byeGroup) > 0 ? $byeGroup[0] : [];
        $sizeCompetitors = sizeof($competitors);
        $sizeGroupBy = sizeof($byeGroup);

        $frequency = $sizeGroupBy != 0
            ? (int)floor($sizeCompetitors / $sizeGroupBy)
            : -1;

        // Create Copy of $competitors
        $newCompetitors = new Collection;
        $i = 0;
        $byeCount = 0;
        foreach ($competitors as $competitor) {

            if ($frequency != -1 && $i % $frequency == 0 && $byeCount < $sizeGroupBy) {
                $newCompetitors->push($bye);
                $byeCount++;
            }
            $newCompetitors->push($competitor);
            $i++;

        }

        return $newCompetitors;
    }
}