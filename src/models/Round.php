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

//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function user1()
//    {
//        return $this->belongsTo(User::class, 'c1', 'id');
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function user2()
//    {
//        return $this->belongsTo(User::class, 'c2', 'id');
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function user3()
//    {
//        return $this->belongsTo(User::class, 'c3', 'id');
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function user4()
//    {
//        return $this->belongsTo(User::class, 'c4', 'id');
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function user5()
//    {
//        return $this->belongsTo(User::class, 'c5', 'id');
//    }
//
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function team1()
//    {
//        return $this->belongsTo(Team::class, 'c1', 'id');
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function team2()
//    {
//        return $this->belongsTo(Team::class, 'c2', 'id');
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
//     */
//    public function team3()
//    {
//        return $this->belongsTo(Team::class, 'c3', 'id');
//    }


    /**
     * @param Collection $tree
     * @param $settings
     * @param Championship $championship
     */
    public static function generateFights(Collection $tree, $settings, Championship $championship = null)
    {

        // Delete previous fight for this championship

        $arrayTreeId = $tree->map(function ($value, $key) {
            return $value->id;
        })->toArray();
        Fight::destroy($arrayTreeId);

        if ($settings->hasPreliminary) {
            if ($settings->preliminaryGroupSize == 3) {
                for ($numRound = 1; $numRound <= $settings->preliminaryGroupSize; $numRound++) {
                    Fight::saveFightRound($tree, $numRound);
                }
            } else {
                Fight::saveRoundRobinFights($championship, $tree);
            }
        } elseif ($settings->treeType == config('kendo-tournaments.DIRECT_ELIMINATION')) {
            Fight::saveFightRound($tree); // Always C1 x C2
        } elseif ($settings->treeType == config('kendo-tournaments.ROUND_ROBIN')) {
            Fight::saveRoundRobinFights($championship, $tree);

        }

    }
}