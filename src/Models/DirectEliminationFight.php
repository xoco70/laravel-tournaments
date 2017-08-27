<?php

namespace Xoco70\LaravelTournaments\Models;

class DirectEliminationFight extends Fight
{
    public function __construct(Fight $fight = null)
    {
        parent::__construct();
        if ($fight != null) {
            $this->id = $fight->id;
            $this->short_id = $fight->short_id;
            $this->fighters_group_id = $fight->fighters_group_id;
            $this->c1 = $fight->c1;
            $this->c2 = $fight->c2;
        }
    }

    /**
     * @param Championship $championship
     */
    public static function saveFights(Championship $championship, $fromRound = 1)
    {
        $round = [];
        $groupsFromRound = $championship->groupsFromRound($fromRound)->get();
        foreach ($groupsFromRound as $group) {
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
                }
            }
        }
    }
}
