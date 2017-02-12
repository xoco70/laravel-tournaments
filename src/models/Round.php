<?php

namespace Xoco70\KendoTournaments\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
        return $this->hasMany(Fight::class, 'tree_id', 'id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'round_team');
    }

    public function competitors()
    {
        return $this->belongsToMany(Competitor::class, 'round_competitor');
    }


    /**
     * @param Collection $rounds
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

        Fight::saveRoundRobinFights($championship, $rounds);


//        if ($settings->hasPreliminary) {
//            if ($settings->preliminaryGroupSize == 3) {
//                for ($numRound = 1; $numRound <= $settings->preliminaryGroupSize; $numRound++) {
//
//                }
//            } else {
//                Fight::saveRoundRobinFights($championship, $tree);
//            }
//        } elseif ($settings->treeType == config('kendo-tournaments.DIRECT_ELIMINATION')) {
//            Fight::saveFightRound($tree); // Always C1 x C2
//        } elseif ($settings->treeType == config('kendo-tournaments.ROUND_ROBIN')) {
//            Fight::saveRoundRobinFights($championship, $tree);
//
//        }

    }
}