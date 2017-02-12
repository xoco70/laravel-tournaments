<?php


namespace Xoco70\KendoTournaments\Models;


use App\User;
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
    private static function getActorsToFights(Championship $championship, Round $round = null)
    {

        if ($championship->category->isTeam) {
            $fighters = $round->teams;
            if (sizeof($fighters) % 2 != 0) {
                $fighters->push(new Team(['name' => "BYE"]));
            }
        } else {
            $fighters = $round->competitors;
            if (sizeof($fighters) % 2 != 0) {
                $fighters->push(new Competitor());
            }
        }
        return $fighters;
    }

    /**
     * Get First Fighter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competitor1()
    {
        return $this->belongsTo(Competitor::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competitor2()
    {
        return $this->belongsTo(Competitor::class, 'c2', 'id');
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

//    /**
//     * Save a Fight.
//     * @param $rounds
//     * @param int $numRound
//     */
//    public static function saveFightRound($rounds, $numRound = 1)
//    {
//
//        $c1 = $c2 = $c3 = null;
//        $order = 0;
//
//        foreach ($rounds as $round) {
//            if ($round->championship->isTeam) {
//                $fighters = $round->teams;
//            } else {
//                $fighters = $round->competitors;
//            }
//            switch ($numRound) {
//                case 1:
//                    $c1 = $round->c1 ?? null;
//                    $c2 = $round->c2 ?? null;
//                    break;
//                case 2:
//                    $c1 = $round->c2 ?? null;
//                    $c2 = $round->c3 ?? null;
//                    break;
//                case 3:
//                    $c1 = $round->c3 ?? null;
//                    $c2 = $round->c1 ?? null;
//                    break;
//            }
//            $fight = new Fight();
//            $fight->tree_id = $round->id;
//            $fight->c1 = $c1;
//            $fight->c2 = $c2;
//            $fight->order = $order++;
//            $fight->area = $round->area;
//            $fight->save();
//        }
//    }


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

    public static function saveRoundRobinFights(Championship $championship, $rounds)
    {


        foreach ($rounds as $round2) {

            $fighters = self::getActorsToFights($championship, $round2);

            $away = $fighters->splice(sizeof($fighters) / 2);  // 2

            $home = $fighters; // 1

            $order = 1;

            for ($i = 0; $i < sizeof($home) + sizeof($away) - 1; $i++) { // 0 -> 2
                for ($j = 0; $j < sizeof($home); $j++) {  // 1 no mas

                    $round[$i][$j]["Home"] = $home[$j];
                    $round[$i][$j]["Away"] = $away[$j];
                    $fight = new Fight();
                    $fight->round_id = $rounds[0]->id;
                    $fight->c1 = $round[$i][$j]["Home"]->id;
                    $fight->c2 = $round[$i][$j]["Away"]->id;
                    $fight->order = $order++;
                    $fight->area = 1;

                    // We ommit fights that have a BYE in Round robins, but not in Preliminary

                    if ($fight->c1 != null && $fight->c2 != null || !$championship->isRoundRobinType()) {
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