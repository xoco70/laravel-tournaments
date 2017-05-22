<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;

class Fighter extends Model
{
    /**
     * Get the Competitor's Championship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    function category()
    {
        return $this->hasManyThrough(Category::class, Championship::class);
    }

    /**
     * @return Competitor|Team
     */
    function fighter()
    {
        if ($this->championship->category->isTeam()) {
            return new Team();
        }
        return new Competitor();

    }
}
