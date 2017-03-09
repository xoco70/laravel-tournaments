<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FightersGroup extends Model
{
    protected $table = 'fighters_groups';
    public $timestamps = true;
    protected $guarded = ['id'];

    /**
     * Check if Request contains tournamentSlug / Should Move to TreeRequest When Built.
     *
     * @param $request
     *
     * @return bool
     */
    public static function hasTournamentInRequest($request)
    {
        return $request->tournament != null;
    }

    /**
     * Check if Request contains championshipId / Should Move to TreeRequest When Built.
     *
     * @param $request
     *
     * @return bool
     */
    public static function hasChampionshipInRequest($request)
    {
        return $request->championshipId != null; // has return false, don't know why
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fights()
    {
        return $this->hasMany(Fight::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'fighters_group_team')->withTimestamps();
    }

    public function competitors()
    {
        return $this->belongsToMany(Competitor::class, 'fighters_group_competitor')->withTimestamps();
    }

    /**
     * @param $settings
     * @param Championship $championship
     */
    public static function generateFights(Collection $fightersGroup, $settings, Championship $championship = null)
    {

        // Delete previous fight for this championship

        $arrGroupsId = $fightersGroup->map(function ($value, $key) {
            return $value->id;
        })->toArray();
        Fight::destroy($arrGroupsId);

        if ($settings->hasPreliminary && $settings->preliminaryGroupSize == 3) {
            for ($numGroup = 1; $numGroup <= $settings->preliminaryGroupSize; $numGroup++) {
                Fight::savePreliminaryFightGroup($fightersGroup, $numGroup);
            }
        } else {
            Fight::saveGroupdRobinFights($championship, $fightersGroup);
        }
    }

    /**
     * Supercharge of sync Many2Many function.
     * Original sync doesn't insert NULL ids.
     *
     * @param $fighters
     */
    public function syncTeams($fighters)
    {
        $this->teams()->detach();
        foreach ($fighters as $fighter) {
            if ($fighter != null) {
                $this->teams()->attach($fighter);
            } else {
                // Insert row manually
                DB::table('fighters_group_team')->insertGetId(
                    ['team_id' => null, 'fighters_group_id' => $this->id]
                );
            }
        }
    }

    /**
     * Supercharge of sync Many2Many function.
     * Original sync doesn't insert NULL ids.
     *
     * @param $fighters
     */
    public function syncCompetitors($fighters)
    {
        $this->competitors()->detach();
        foreach ($fighters as $fighter) {
            if ($fighter != null) {
                $this->competitors()->attach($fighter);
            } else {
                DB::table('fighters_group_competitor')->insertGetId(
                    ['competitor_id' => null, 'fighters_group_id' => $this->id]
                );
            }
        }
    }
}
