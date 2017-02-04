<?php

namespace Xoco70\LaravelTournaments;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    protected $table = 'team';
    public $timestamps = true;
    protected $fillable = ['name', 'championship_id'];


    /**
     * A Team belongs to a Championship
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
}