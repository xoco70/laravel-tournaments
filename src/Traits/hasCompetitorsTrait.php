<?php

namespace Xoco70\LaravelTournaments\Traits;


use Illuminate\Support\Collection;
use Xoco70\LaravelTournaments\Models\Competitor;

trait hasCompetitorsTrait
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

    protected function createByeFighter()
    {
        return new Competitor();
    }

}