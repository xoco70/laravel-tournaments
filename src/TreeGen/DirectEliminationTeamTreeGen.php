<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Models\Team;

class DirectEliminationTeamTreeGen extends DirectEliminationTreeGen
{
    /**
     * get Fighter by Id.
     *
     * @return Team
     */
    protected function getFighter($teamId)
    {
        return Team::find($teamId);
    }

    /**
     * Fighter is the name for competitor or team, depending on the case.
     *
     * @return Collection
     */
    protected function getFighters()
    {
        return $this->championship->teams;
    }

    /**
     * @param FightersGroup $group
     * @param $fighters
     *
     * @return FightersGroup
     */
    public function syncGroup(FightersGroup $group, $fighters)
    {
        // Add all competitors to Pivot Table
        return $group->syncTeams($fighters);
    }

    protected function createByeFighter()
    {
        return new Team();
    }

    protected function addFighterToGroup(FightersGroup $group, $team)
    {
        $group->teams()->attach($team->id);
    }
}
