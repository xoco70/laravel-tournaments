<?php

namespace Xoco70\LaravelTournaments\Models;

class Team extends Fighter
{
    protected $table = 'team';
    public $timestamps = true;
    protected $fillable = ['short_id', 'name', 'championship_id'];



    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fightersGroups()
    {
        return $this->belongsToMany(FightersGroup::class, 'fighters_group_team')->withTimestamps();
    }

    public function competitors()
    {
        return $this->belongsToMany(Competitor::class)->withTimestamps();
    }

    /**
     * Get Team Name
     * @return mixed|string
     */
    public function getNameAttribute()
    {
        return ($this->hasAttribute('name')) ? $this->attributes['name'] : "";
    }
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }
}
