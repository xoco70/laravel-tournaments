<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Support\Collection;

class PreliminaryFight extends Fight
{

    public function __construct(Fight $fight = null)
    {
        if ($fight!=null){
            $this->id= $fight->id;
            $this->short_id= $fight->short_id;
            $this->fighters_group_id= $fight->fighters_group_id;
            $this->c1= $fight->c1;
            $this->c2= $fight->c2;
        }
    }

    /**
     * Save a Fight.
     *
     * @param Collection $groups
     * @param int $numGroup
     */
    public static function saveFights(Collection $groups, $numGroup = 1)
    {
        $competitor1 = $competitor2 = null;
        $order = 1;

        foreach ($groups as $group) {

            $fighters = $group->getFightersWithBye();

            $fighter1 = $fighters->get(0);
            $fighter2 = $fighters->get(1);
            $fighter3 = $fighters->get(2);

            switch ($numGroup) {
                case 1:
                    $competitor1 = $fighter1;
                    $competitor2 = $fighter2;
                    break;
                case 2:
                    $competitor1 = $fighter2;
                    $competitor2 = $fighter3;
                    break;
                case 3:
                    $competitor1 = $fighter3;
                    $competitor2 = $fighter1;
                    break;
            }
            $order = self::saveFight($group, $competitor1, $competitor2, $order);
        }
    }

    /**
     * @param $group
     * @param $competitor1
     * @param $competitor2
     * @param $order
     * @return mixed
     */
    private static function saveFight($group, $competitor1, $competitor2, $order)
    {
        $fight = new Fight();
        $fight->fighters_group_id = $group->id;
        $fight->c1 = $competitor1 != null ? $competitor1->id : null;
        $fight->c2 = $competitor2 != null ? $competitor2->id : null;
        $fight->short_id = $order++;
        $fight->area = $group->area;
        $fight->save();
        return $order;
    }
}