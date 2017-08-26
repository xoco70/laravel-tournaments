<?php

namespace Xoco70\LaravelTournaments\Models;

use Illuminate\Database\Eloquent\Model;

class Fighter extends Model
{
    /**
     * Get the Competitor's Championship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function category()
    {
        return $this->hasManyThrough(Category::class, Championship::class);
    }

    public function getFullNameAttribute()
    {
        if ($this instanceof Competitor) {
            return $this->getFullName();
        }

        return $this->name;
    }
}
