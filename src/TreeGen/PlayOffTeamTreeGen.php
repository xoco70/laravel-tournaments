<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Team;

class PlayOffTeamTreeGen extends PlayOffTreeGen
{

    /**
     * get Fighter by Id
     * @return Team
     */
    protected function getFighter($teamId)
    {
        return Team::find($teamId);
    }
    /**
     * Fighter is the name for competitor or team, depending on the case
     * @return Collection
     */
    protected function getFighters()
    {
        return $this->championship->teams;
    }

    /**
     * @param FightersGroup $group
     * @param $fighters
     * @return FightersGroup
     */
    public function syncGroup(FightersGroup $group, $fighters)
    {
        // Add all competitors to Pivot Table
        $group->syncTeams($fighters);
        return $group;
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
