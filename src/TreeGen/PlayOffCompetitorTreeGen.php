<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\FighterGroupCompetitor;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Traits\hasCompetitorsTrait;

class PlayOffCompetitorTreeGen extends PlayOffTreeGen
{

    use hasCompetitorsTrait;

    /**
     * @param FightersGroup $group
     * @param $fighters
     *
     * @return FightersGroup
     */
    public function syncGroup(FightersGroup $group, $fighters)
    {
        // Add all competitors to Pivot Table
        return $group->syncCompetitors($fighters);
    }


    /**
     * @param FightersGroup $group
     * @param $competitor
     * @param $fighterToUpdate
     * Common to All Playoff and singleElim
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
