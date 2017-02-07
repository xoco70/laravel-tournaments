<?php


namespace Xoco70\LaravelTournaments\Models;


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
        'tree_id',
        'c1',
        'c2'
    ];

    /**
     * @param Championship $championship
     * @return mixed
     */
    private static function getActorsToFights(Championship $championship, Tree $treeGroup = null)
    {
        if ($treeGroup->c1 == null && $treeGroup->c2 == null && $treeGroup->c3 == null && $treeGroup->c4 == null && $treeGroup->c5 == null) { // This is a Preliminary Tree with comp > 3 We get Fighters from tree
            if ($championship->category->isTeam) {
                $fighters = $championship->teams;
            } else {
                $fighters = $championship->users;
            }
        } else {
            $fighters = new Collection;
            if ($championship->category->isTeam) {
                if ($treeGroup->c1 != null) $fighters->push($treeGroup->team1);
                if ($treeGroup->c2 != null) $fighters->push($treeGroup->team2);
                if ($treeGroup->c3 != null) $fighters->push($treeGroup->team3);
                if ($treeGroup->c4 != null) $fighters->push($treeGroup->team4);
                if ($treeGroup->c5 != null) $fighters->push($treeGroup->team5);
            } else {
                if ($treeGroup->c1 != null) $fighters->push($treeGroup->user1);
                if ($treeGroup->c2 != null) $fighters->push($treeGroup->user2);
                if ($treeGroup->c3 != null) $fighters->push($treeGroup->user3);
                if ($treeGroup->c4 != null) $fighters->push($treeGroup->user4);
                if ($treeGroup->c5 != null) $fighters->push($treeGroup->user5);
            }
        }
        if ($championship->category->isTeam) {
            if (sizeof($fighters) % 2 != 0) {
                $fighters->push(new Team(['name' => "BYE"]));
            }
        } else {
            if (sizeof($fighters) % 2 != 0) {
                $fighters->push(new User(['name' => "BYE"]));
            }
        }
        return $fighters;
    }

    /**
     * Get First Fighter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'c2', 'id');
    }

    /**
     * Get First Fighter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team1()
    {
        return $this->belongsTo(Team::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team2()
    {
        return $this->belongsTo(Team::class, 'c2', 'id');
    }

    /**
     * Save a Fight.
     * @param $tree
     * @param int $numRound
     */
    public static function saveFightRound($tree, $numRound = 1)
    {

        $c1 = $c2 = $c3 = null;
        $order = 0;

        foreach ($tree as $treeGroup) {

            switch ($numRound) {
                case 1:
                    $c1 = $treeGroup->c1 ?? null;
                    $c2 = $treeGroup->c2 ?? null;
                    break;
                case 2:
                    $c1 = $treeGroup->c2 ?? null;
                    $c2 = $treeGroup->c3 ?? null;
                    break;
                case 3:
                    $c1 = $treeGroup->c3 ?? null;
                    $c2 = $treeGroup->c1 ?? null;
                    break;
            }
            $fight = new Fight();
            $fight->tree_id = $treeGroup->id;
            $fight->c1 = $c1;
            $fight->c2 = $c2;
            $fight->order = $order++;
            $fight->area = $treeGroup->area;
            $fight->save();
        }
    }


//    public static function saveRoundRobinFight(Championship $championship, $tree)
//    {
//        $championship->category->isTeam
//            ? $fighters = $championship->teams
//            : $fighters = $championship->users;
//
//        $reverseFighters = $fighters->reverse();
//        $order = 0;
//        foreach ($reverseFighters as $reverse) {
//            foreach ($fighters as $fighter) {
//                if ($fighter->id != $reverse->id) {
//                    $fight = new Fight();
//                    $fight->tree_id = $tree[0]->id;
//                    $fight->c1 = $fighter->id;
//                    $fight->c2 = $reverse->id;
//                    $fight->order = $order++;
//                    $fight->area = 1;
//                    $fight->save();
//                    $order++;
//                }
//            }
//        }
//    }

    public static function saveRoundRobinFights(Championship $championship, $tree)
    {


        foreach ($tree as $treeGroup) {
            $round = [];
            $fighters = self::getActorsToFights($championship, $treeGroup);

            $away = $fighters->splice(sizeof($fighters) / 2);  // 2

            $home = $fighters; // 1

            $order = 1;

            for ($i = 0; $i < sizeof($home) + sizeof($away) - 1; $i++) { // 0 -> 2
                for ($j = 0; $j < sizeof($home); $j++) {  // 1 no mas

                    $round[$i][$j]["Home"] = $home[$j];
                    $round[$i][$j]["Away"] = $away[$j];
                    $fight = new Fight();
                    $fight->tree_id = $tree[0]->id;
                    $fight->c1 = $round[$i][$j]["Home"]->id;
                    $fight->c2 = $round[$i][$j]["Away"]->id;
                    $fight->order = $order++;
                    $fight->area = 1;

                    if ($fight->c1 != null && $fight->c2 != null) { // We ommit fights that have a BYE
                        $fight->save();
                    }
                }
                if (sizeof($home) + sizeof($away) - 1 > 2) {
                    $away->prepend($home->splice(1, 1)->shift());
                    $home->push($away->pop());
                    $order++;
                }
            }
//            return $round;
        }

    }

}