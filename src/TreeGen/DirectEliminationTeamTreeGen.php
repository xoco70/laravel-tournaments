<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Team;

class DirectEliminationTeamTreeGen extends TreeGen
{
    /**
     * Create Bye Groups to adjust tree
     * @param $byeCount
     * @return Collection
     */
    protected function createNullsGroup($byeCount): Collection
    {
        $null = new Team();
        $byeGroup = new Collection();
        for ($i = 0; $i < $byeCount; $i++) {
            $byeGroup->push($null);
        }
        return $byeGroup;
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
     * @param $group
     * @param $fighters
     * @return FightersGroup
     */
    public function syncGroup($group, $fighters)
    {
        // Add all competitors to Pivot Table
        $group->syncTeams($fighters);
        return $group;
    }

    protected function createByeFighter()
    {
        return new Team();
    }
}
