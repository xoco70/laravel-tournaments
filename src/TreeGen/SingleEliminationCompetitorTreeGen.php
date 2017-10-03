<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Xoco70\LaravelTournaments\Models\FighterGroupCompetitor;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Traits\hasCompetitorsTrait;

class SingleEliminationCompetitorTreeGen extends SingleEliminationTreeGen
{
    use hasCompetitorsTrait;

    /**
     * @param $group
     * @param $fighters
     */
    public function syncGroup(FightersGroup $group, $fighters)
    {
        return $group->syncCompetitors($fighters);
    }

    /**
     * @param FightersGroup $group
     * @param $competitor
     * @param $fighterToUpdate
     */
    protected function addFighterToGroup(FightersGroup $group, $competitor, $fighterToUpdate)
    {
        // fighterToUpdate is coming at format c1, c2, c3 etc.
        $numFighterToUpdate = substr($fighterToUpdate, 1);
        $competitorGroup = FighterGroupCompetitor::where('fighters_group_id', $group->id)
            ->get()
            ->get($numFighterToUpdate - 1);

        // we must update the right result
        $competitorGroup->competitor_id = $competitor->id;
        $competitorGroup->save();
    }
}
