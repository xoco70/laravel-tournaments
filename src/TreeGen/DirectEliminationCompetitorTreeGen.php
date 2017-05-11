<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Exceptions\TreeGenerationException;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Team;

class DirectEliminationCompetitorTreeGen extends DirectEliminationTreeGen
{
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
     * @return FightersGroup
     */
    public function syncGroup($group, $fighters)
    {
        // Add all competitors to Pivot Table
        $group->syncCompetitors($fighters);
        return $group;
    }

    protected function createByeFighter()
    {
        return new Competitor();
    }
}
