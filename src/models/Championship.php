<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Championship extends Model
{
    use SoftDeletes;
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'championship';

    public $timestamps = true;
    protected $fillable = [
        "tournament_id",
        "category_id",
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($championship) {
            $championship->competitors()->delete();
            $championship->settings()->delete();
        });
        static::restoring(function ($championship) {
            $championship->competitors()->restore();
            $championship->settings()->restore();

        });
    }

    /**
     * A championship has many Competitors
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function competitors()
    {
        return $this->hasMany(Competitor::class);
    }

    /**
     * A championship belongs to a Category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A championship belongs to a Tournament
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }


    /**
     * Get All competitors from a Championships
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'competitor', 'championship_id')
            ->withPivot('confirmed')
            ->withTimestamps();
    }

    /**
     * A championship only has 1 Settings
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function settings()
    {
        return $this->hasOne(ChampionshipSettings::class);
    }

    /**
     * A championship has Many Teams
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }


    /**
     * Check if Championship has Preliminary Round Configured
     * @return bool
     */
    public function hasPreliminary()
    {
        return ($this->settings == null || $this->settings->hasPreliminary);
    }

    /**
     * Check if 2nd Round of Championship is Round Robin
     * @return bool
     */
    public function isRoundRobinType()
    {
        return ($this->settings != null && $this->settings->treeType == Config::get('kendo-tournaments.ROUND_ROBIN'));
    }

    /**
     * Check if 2nd Round of Championship is Direct Elimination
     * @return bool
     */
    public function isDirectEliminationType()
    {
        return ($this->settings == null || $this->settings->treeType == Config::get('kendo-tournaments.DIRECT_ELIMINATION'));
    }

    /**
     * A championship has Many Rounds
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    /**
     * A championship has Many fights
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fights()
    {
        return $this->hasManyThrough(Fight::class, Round::class);
    }



}
