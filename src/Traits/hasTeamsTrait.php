<?php

namespace Xoco70\LaravelTournaments\Traits;

use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\Team;

trait hasTeamsTrait
{
    /**
     * get Fighter by Id.
     *
     * @param $teamId
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

    protected function createByeFighter()
    {
        return new Team();
    }
}
