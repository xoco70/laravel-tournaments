<?php

namespace Xoco70\KendoTournaments\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Round extends Model
{
    protected $table = 'round';
    public $timestamps = true;
    protected $guarded = ['id'];

    /**
     * Check if Request contains tournamentSlug / Should Move to TreeRequest When Built
     * @param $request
     * @return bool
     */
    public static function hasTournamentInRequest($request)
    {
        return $request->tournament != null;
    }

    /**
     * Check if Request contains championshipId / Should Move to TreeRequest When Built
     * @param $request
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
        return $this->belongsToMany(Team::class, 'round_team')->withTimestamps();
    }

    public function competitors()
    {
        return $this->belongsToMany(Competitor::class, 'round_competitor')->withTimestamps();
    }


    /**
     * @param $settings
     * @param Championship $championship
     */
    public static function generateFights(Collection $rounds, $settings, Championship $championship = null)
    {

        // Delete previous fight for this championship

        $arrRoundsId = $rounds->map(function ($value, $key) {
            return $value->id;
        })->toArray();
        Fight::destroy($arrRoundsId);

        if ($settings->hasPreliminary && $settings->preliminaryGroupSize == 3) {

            for ($numRound = 1; $numRound <= $settings->preliminaryGroupSize; $numRound++) {
                Fight::savePreliminaryFightRound($rounds, $numRound);
            }
        } else {
            Fight::saveRoundRobinFights($championship, $rounds);
        }
    }


    /**
     * Supercharge of sync Many2Many function.
     * Original sync doesn't insert NULL ids
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
                DB::table('round_team')->insertGetId(
                    ['team_id' => null, 'round_id' => $this->id]
                );
            }
        }
    }

    /**
     * Supercharge of sync Many2Many function.
     * Original sync doesn't insert NULL ids
     * @param $fighters
     */
    public function syncCompetitors($fighters)
    {
        $this->competitors()->detach();
        foreach ($fighters as $fighter) {
            if ($fighter != null) {
                $this->competitors()->attach($fighter);
            } else {
                DB::table('round_competitor')->insertGetId(
                    ['competitor_id' => null, 'round_id' => $this->id]
                );
            }
        }
    }
}