<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

class Championship extends Model
{
    use SoftDeletes;
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'championship';

    public $timestamps = true;
    protected $fillable = [
        'tournament_id',
        'category_id',
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
     * A championship has many Competitors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function competitors()
    {
        return $this->hasMany(Competitor::class);
    }

    /**
     * A championship has many Competitors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function competitorsWithUser()
    {
        return $this->hasMany(Competitor::class)->with('user');
    }

    /**
     * A championship belongs to a Category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A championship belongs to a Tournament.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get All competitors from a Championships.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'competitor', 'championship_id')
            ->withPivot('confirmed')
            ->withTimestamps();
    }

    /**
     * A championship only has 1 Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function settings()
    {
        return $this->hasOne(ChampionshipSettings::class);
    }

    /**
     * A championship has Many Teams.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Check if Championship has Preliminary Round Configured.
     *
     * @return bool
     */
    public function hasPreliminary()
    {
        return $this->settings == null || $this->settings->hasPreliminary;
    }

    /**
     * Check if 2nd Round of Championship is Round Robin.
     *
     * @return bool
     */
    public function isPlayOffType()
    {
        return $this->settings != null && $this->settings->treeType == ChampionshipSettings::PLAY_OFF;
    }

    /**
     * Check if 2nd Round of Championship is Direct Elimination.
     *
     * @return bool
     */
    public function isDirectEliminationType()
    {
        return $this->settings != null && $this->settings->treeType == ChampionshipSettings::DIRECT_ELIMINATION;
    }

    /**
     * A championship has Many Groups of Fighters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fightersGroups()
    {
        return $this->hasMany(FightersGroup::class);
    }

    /**
     * A championship has Many fights.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fights()
    {
        return $this->hasManyThrough(Fight::class, FightersGroup::class);
    }

    /**
     * Get the fights that happen to the first round
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function firstRoundFights()
    {
        return $this->hasManyThrough(Fight::class, FightersGroup::class)
            ->where('fighters_groups.round', 1)
            ->orderby('fighters_groups.id', 'desc');
    }

    private function hasNoCustomSettings()
    {
        return
            $this->settings == null ||
            ($this->settings->ageCategory == null || $this->settings->ageCategory == 0) &&
            $this->settings->ageMin == null &&
            $this->settings->ageMax == null &&
            $this->settings->gradeMin == null &&
            $this->settings->gradeMax == null &&
            ($this->settings->gradeCategory == null || $this->settings->gradeCategory == 0);
    }

    public function buildName()
    {
        if ($this->settings != null && $this->settings->alias != null && $this->settings->alias != '')
            return $this->settings->alias;

        if ($this->hasNoCustomSettings()) {
            return $this->category->name;
        }

        $genders = [
            'M' => trans('categories.male'),
            'F' => trans('categories.female'),
            'X' => trans('categories.mixt')
        ];

        $teamText = $this->category->isTeam == 1 ? trans_choice('core.team', 1) : trans('categories.single');
        $ageCategoryText = $this->category->getAgeString();
        $gradeText = $this->category->getGradeString();

        return $teamText . ' ' . $genders[$this->category->gender] . ' ' . $ageCategoryText . ' ' . $gradeText;
    }

    public function getSettings()
    {
        return $setting = $this->settings ?? new ChampionshipSettings(ChampionshipSettings::DEFAULT_SETTINGS);
    }

    /**
     * Return Groups that belongs to a round
     * @param $round
     * @return HasMany
     */
    public function groupsByRound($round)
    {
        return $this->fightersGroups()->where('round',$round);
    }

    /**
     * Return Fights that belongs to a round
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fightsByRound($round)
    {
        return $this->hasManyThrough(Fight::class, FightersGroup::class)->where('round',$round);
    }
}
