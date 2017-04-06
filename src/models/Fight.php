<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Fight extends Model
{
    /**
     * Fight constructor.
     */
    public function __construct($userId1 = null, $userId2 = null)
    {
        $this->c1 = $userId1;
        $this->c2 = $userId2;
    }

    protected $table = 'fight';
    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'c1',
        'c2',
    ];

    /**
     * Get First Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(\Xoco70\KendoTournaments\Models\FightersGroup::class, 'fighters_group_id');
    }
    /**
     * @param Championship $championship
     *
     * @return mixed
     */
    private static function getActorsToFights(\Xoco70\KendoTournaments\Models\Championship $championship, FightersGroup $group = null)
    {
        if ($championship->category->isTeam) {
            $fighters = $group->teams;
            if (sizeof($fighters) == 0) {
                $fighters->push(new Team());
                $fighters->push(new Team());
            } else if (count($fighters) % 2 != 0) {
                $fighters->push(new Team(['name' => 'BYE']));
            }

        } else {
            $fighters = $group->competitors;
            if (sizeof($fighters) == 0) { // If
                $fighters->push(new Competitor());
                $fighters->push(new Competitor());
            } else if (count($fighters) % 2 != 0) { // If fighter is not pair, add a BYE
                $fighters->push(new Competitor());
            }


        }

        return $fighters;
    }

    /**
     * Get First Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competitor1()
    {
        return $this->belongsTo(\Xoco70\KendoTournaments\Models\Competitor::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competitor2()
    {
        return $this->belongsTo(\Xoco70\KendoTournaments\Models\Competitor::class, 'c2', 'id');
    }

    /**
     * Get First Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team1()
    {
        return $this->belongsTo(\Xoco70\KendoTournaments\Models\Team::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team2()
    {
        return $this->belongsTo(\Xoco70\KendoTournaments\Models\Team::class, 'c2', 'id');
    }

    /**
     * Save a Fight.
     *
     * @param Collection $groups
     * @param int $numGroup
     */
    public static function savePreliminaryFightGroup($groups, $numGroup = 1)
    {

        $c1 = $c2 = null;
        $order = 0;

        foreach ($groups as $group) {

            if ($group->championship->category->isTeam()) {
                $fighters = $group->teams;
            } else {
                $fighters = $group->competitors;
            }

            $fighter1 = $fighters->get(0);
            $fighter2 = $fighters->get(1);
            $fighter3 = $fighters->get(2);

            switch ($numGroup) {
                case 1:
                    $c1 = $fighter1;
                    $c2 = $fighter2;
                    break;
                case 2:
                    $c1 = $fighter2;
                    $c2 = $fighter3;
                    break;
                case 3:
                    $c1 = $fighter3;
                    $c2 = $fighter1;
                    break;
            }
            $fight = new self();
            $fight->fighters_group_id = $group->id;
            $fight->c1 = $c1 != null ? $c1->id : null;
            $fight->c2 = $c2 != null ? $c2->id : null;
            $fight->order = $order++;
            $fight->area = $group->area;
            $fight->save();
        }
    }


    /**
     * @param Championship $championship
     * @param Collection $groups
     */
    public static function saveGroupFights(\Xoco70\KendoTournaments\Models\Championship $championship, $groups)
    {
        foreach ($groups as $group) {
            $fighters = self::getActorsToFights($championship, $group);

            $away = $fighters->splice(count($fighters) / 2); // 2

            $home = $fighters; // 1

            $order = 1;

            for ($i = 0; $i < count($home) + count($away) - 1; $i++) { // 0 -> 2
                for ($j = 0; $j < count($home); $j++) {  // 1 no mas

                    $round[$i][$j]['Home'] = $home[$j];
                    $round[$i][$j]['Away'] = $away[$j];
                    $fight = new self();
                    $fight->fighters_group_id = $group->id;
                    $fight->c1 = $round[$i][$j]['Home']->id;
                    $fight->c2 = $round[$i][$j]['Away']->id;
                    $fight->order = $order++;
                    $fight->area = 1;

                    $fight->save();

                }
                if (count($home) + count($away) - 1 > 2) {
                    $away->prepend($home->splice(1, 1)->shift());
                    $home->push($away->pop());
                    $order++;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getFighter1Name()
    {
        $isTeam = $this->group->championship->category->isTeam;
        if ($isTeam) {

            return $this->team1 == null ? '' : $this->team1->name;
        }
        return
            $this->competitor1 == null ? 'BYE' : $this->competitor1->user->firstname . " " . $this->competitor1->user->lastname;

    }

    /**
     * @return string
     */
    public function getFighter2Name()
    {
        $isTeam = $this->group->championship->category->isTeam;
        if ($isTeam) {

            return $this->team2 == null ? 'BYE' : $this->team2->name;
        }

        return
            $this->competitor2 == null ? 'BYE' : $this->competitor2->user->firstname . " " . $this->competitor2->user->lastname;
    }


    /**
     * @return string
     */
    public function getFighter1ShortId()
    {

        $isTeam = $this->group->championship->category->isTeam;
        if ($isTeam) {
            return $this->team1 == null ? '' : $this->team1->short_id;
        }

        return $this->competitor1 == null ? '' : $this->competitor1->short_id;

    }

    /**
     * @return string
     */
    public function getFighter2ShortId()
    {

        $isTeam = $this->group->championship->category->isTeam;
        if ($isTeam) {
            return $this->team2 == null ? '' : $this->team2->short_id;
        }

        return $this->competitor2 == null ? '' : $this->competitor2->short_id;

    }
}