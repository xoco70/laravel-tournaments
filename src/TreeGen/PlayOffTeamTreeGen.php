<?php

namespace Xoco70\LaravelTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\FighterGroupTeam;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Models\Team;
use Xoco70\LaravelTournaments\Traits\hasTeamsTrait;

class PlayOffTeamTreeGen extends PlayOffTreeGen
{
    use hasTeamsTrait;
    /**
     * @param FightersGroup $group
     * @param $fighters
     *
     * @return FightersGroup
     */
    public function syncGroup(FightersGroup $group, $fighters)
    {
        // Add all competitors to Pivot Table
        $group->syncTeams($fighters);

        return $group;
    }

    /**
     * @param FightersGroup $group
     * @param $team
     * @param $fighterToUpdate
     */
    protected function addFighterToGroup(FightersGroup $group, $team, $fighterToUpdate)
    {
        // fighterToUpdate is coming at format c1, c2, c3 etc.
        $numFighterToUpdate = substr($fighterToUpdate, 1);
        $competitorGroup = FighterGroupTeam::where('fighters_group_id', $group->id)
            ->get()
            ->get($numFighterToUpdate - 1);

        // we must update the right result
        $competitorGroup->team_id = $team->id;
        $competitorGroup->save();
    }
}
