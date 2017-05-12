<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Illuminate\Support\Collection;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\FightersGroup;

class PlayOffCompetitorTreeGen extends PlayOffTreeGen
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
        return $group->syncCompetitors($fighters);
    }

    protected function createByeFighter()
    {
        return new Competitor();
    }
}
