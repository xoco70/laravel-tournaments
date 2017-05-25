<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DirectEliminationFight extends Fight
{

    /**
     * @param Championship $championship
     */
    public static function saveFights(Championship $championship)
    {
        $round = [];
        foreach ($championship->fightersGroups()->get() as $group) {
            $fighters = parent::getFightersWithByes($group);
            $away = $fighters->splice(count($fighters) / 2); // 2
            $home = $fighters; // 1

            $countHome = count($home);
            $countAway = count($away);
            for ($i = 0; $i < $countHome + $countAway - 1; $i++) {
                for ($j = 0; $j < $countHome; $j++) {
                    $round[$i][$j]['Home'] = $home[$j];
                    $round[$i][$j]['Away'] = $away[$j];
                    $fight = new Fight();
                    $fight->fighters_group_id = $group->id;
                    $fight->c1 = $round[$i][$j]['Home']->id;
                    $fight->c2 = $round[$i][$j]['Away']->id;
                    $fight->area = $group->area;
                    $fight->save();

                }
                if ($countHome + $countAway - 1 > 2) {
                    $away->prepend($home->splice(1, 1)->shift());
                    $home->push($away->pop());
//                    $order++;
                }
            }
        }
    }
}