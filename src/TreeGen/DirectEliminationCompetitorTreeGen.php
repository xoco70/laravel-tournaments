<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\FightersGroup;

class DirectEliminationCompetitorTreeGen extends DirectEliminationTreeGen
{

    /**
     * get Fighter by Id
     * @param $competitorId
     * @return Competitor
     */
    protected function getFighter($competitorId)
    {
        return Competitor::find($competitorId);
    }

    /**
     * Fighter is the name for competitor or team, depending on the case
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

    protected function addFighterToGroup(FightersGroup $group, $competitor)
    {
        $group->competitors()->attach($competitor->id);
    }
}
