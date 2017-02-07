<?php

namespace Xoco70\LaravelTournaments\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Xoco70\LaravelTournaments\TreeGen\PreliminaryTreeGen;

class Tree extends Model
{
    protected $table = 'tree';
    public $timestamps = true;
    protected $guarded = ['id'];

    /**
     * Check if Request contains tournamentSlug / Should Move to TreeRequest When Built
     * @param $request
     * @return bool
     */
    public static function hasTournament($request)
    {
        return $request->tournamentSlug != null;
    }

    /**
     * Check if Request contains championshipId / Should Move to TreeRequest When Built
     * @param $request
     * @return bool
     */
    public static function hasChampionship($request)
    {
        return $request->championshipId != null; // has return false, don't know why
    }

    /**
     * Get tournament with a lot of stuff Inside - Should Change name
     * @param $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getTournament($request)
    {
        $tournament = new Tournament;
        if (Tree::hasTournament($request)) {
            $tournamentSlug = $request->tournamentSlug;
            $tournament = Tournament::with(
                'championships.settings',
                'championships.category',
                'championships.tree.user1',
                'championships.tree.user2',
                'championships.tree.user3',
                'championships.tree.user4',
                'championships.tree.user5'

            )->where('slug', $tournamentSlug)
                ->first();
        } elseif (Tree::hasChampionship($request)) {
            $tournament = Tournament::whereHas('championships', function ($query) use ($request) {
                return $query->where('id', $request->championshipId);
            })
                ->with(['championships' => function ($query) use ($request) {
                    $query->where('id', '=', $request->championshipId)
                        ->with([
                            'settings',
                            'category',
                            'tree' => function ($query) {
                                return $query->with('user1', 'user2', 'user3', 'user4', 'user5');
                            }]);
                }])
                ->first();

        }

        return $tournament;
    }


    /**
     * Get Championships with a lot of stuff Inside - Should Change name
     * @param $request
     * @return Collection
     */
    public static function getChampionships($request)
    {

        $championships = new Collection();
        if (Tree::hasChampionship($request)) {
            $championship = Championship::with('settings', 'category')->find($request->championshipId);
            $championships->push($championship);
        } else if (Tree::hasTournament($request)) {

            $tournament = Tournament::with(
                'championships.settings',
                'championships.category',
                'championships.tree.user1',
                'championships.tree.user2',
                'championships.tree.user3',
                'championships.tree.user4',
                'championships.tree.user5'

            )->where('slug', $request->tournamentId)->first();

            $championships = $tournament->championships;
        }
        return $championships;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'c1', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'c2', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user3()
    {
        return $this->belongsTo(User::class, 'c3', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user4()
    {
        return $this->belongsTo(User::class, 'c4', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user5()
    {
        return $this->belongsTo(User::class, 'c5', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team1()
    {
        return $this->belongsTo(Team::class, 'c1', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team2()
    {
        return $this->belongsTo(Team::class, 'c2', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team3()
    {
        return $this->belongsTo(Team::class, 'c3', 'id');
    }

    /**
     * Define Strategy depending on Tournament Type ( International, National, etc. )
     * @param Championship $championship
     * @return PreliminaryTreeGen|null
     */
    public static function getGenerationStrategy(Championship $championship)
    {
        $tournament = $championship->tournament;
        switch ($tournament->level_id) {
            case Config::get('constants.ND'):
                return new PreliminaryTreeGen($championship, null);
                break;
            case Config::get('constants.local'):
                return new PreliminaryTreeGen($championship, null);
                break;
            case Config::get('constants.district'):
                return new PreliminaryTreeGen($championship, 'club_id');
                break;
            case Config::get('constants.city'):
                return new PreliminaryTreeGen($championship, 'club_id');
                break;
            case Config::get('constants.state'):
                return new PreliminaryTreeGen($championship, 'club_id');
                break;
            case Config::get('constants.regional'):
                return new PreliminaryTreeGen($championship, 'club_id');
                break;
            case Config::get('constants.national'):
                return new PreliminaryTreeGen($championship, 'association_id');
                break;
            case Config::get('constants.international'):
                return new PreliminaryTreeGen($championship, 'federation_id');
                break;
        }
        return null;

        // get Area number
        // get tournament type, and get criteria to repart
        // repart into areas
        // Shuffle
        // Analyse Number of competitors to repart Byes
        // Store / Print

    }

    /**
     * @param Collection $tree
     * @param $settings
     */
    public static function generateFights(Collection $tree, $settings, Championship $championship = null)
    {

        // Delete previous fight for this championship

        $arrayTreeId = $tree->map(function ($value, $key) {
            return $value->id;
        })->toArray();
        Fight::destroy($arrayTreeId);

        if ($settings->hasPreliminary) {
            if ($settings->preliminaryGroupSize == 3){
                for ($numRound = 1; $numRound <= $settings->preliminaryGroupSize; $numRound++) {
                    Fight::saveFightRound($tree, $numRound);
                }
            }else{
                Fight::saveRoundRobinFights($championship, $tree);
            }
        } elseif ($settings->treeType == config('constants.DIRECT_ELIMINATION')) {
            Fight::saveFightRound($tree); // Always C1 x C2
        } elseif ($settings->treeType == config('constants.ROUND_ROBIN')) {
            Fight::saveRoundRobinFights($championship, $tree);

        }

    }
}