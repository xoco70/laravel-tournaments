<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\FighterGroupCompetitor;
use Xoco70\LaravelTournaments\Models\FightersGroup;

class SingleEliminationCompetitorTreeGen extends SingleEliminationTreeGen
{
    /**
     * get Fighter by Id.
     *
     * @param $competitorId
     *
     * @return Competitor
     */
    protected function getFighter($competitorId)
    {
        return Competitor::find($competitorId);
    }

    /**
     * Fighter is the name for competitor or team, depending on the case.
     *
     * @return Collection
     */
    protected function getFighters()
    {
        return $this->championship->competitors;
    }

    /**
     * @param $group
     * @param $fighters
     */
    public function syncGroup(FightersGroup $group, $fighters)
    {
        return $group->syncCompetitors($fighters);
    }

    protected function createByeFighter()
    {
        return new Competitor();
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
