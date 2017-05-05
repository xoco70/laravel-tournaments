<?php

namespace Xoco70\KendoTournaments\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NodeTrait;
use Xoco70\KendoTournaments\TreeGen\TreeGen;

class FightersGroup extends Model
{
    protected $table = 'fighters_groups';
    public $timestamps = true;
    protected $guarded = ['id'];

    use NodeTrait;

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
     * @param Collection $fightersGroup
     * @param $championship->settings
     */
    public static function generateFights(Collection $fightersGroup, Championship $championship)
    {
        // Delete previous fight for this championship

        $arrGroupsId = $fightersGroup->map->id->toArray();
        Fight::destroy($arrGroupsId);

        if ($championship->settings->hasPreliminary && $championship->settings->preliminaryGroupSize == 3) {
            for ($numGroup = 1; $numGroup <= $championship->settings->preliminaryGroupSize; $numGroup++) {
                Fight::savePreliminaryFightGroup($fightersGroup, $numGroup);
            }
        } else {
            Fight::saveGroupFights($championship, $fightersGroup);
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
                    ['competitor_id' => null, 'fighters_group_id' => $this->id,
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ]
                );
            }
        }
    }


    /**
     * Get the many 2 many relationship with
     * @return Collection
     */
    public function competitorsWithNull()
    {
        $competitors = new Collection();
        $fgcs = FighterGroupCompetitor::where('fighters_group_id', $this->id)
            ->with('competitor')
            ->get();
        foreach ($fgcs as $fgc) {
            $competitors->push($fgc->competitor ?? new Competitor());
        }

        return $competitors;

    }


    public function teamsWithNull()
    {
        $teams = new Collection();
        $fgcs = FighterGroupTeam::where('fighters_group_id', $this->id)
            ->with('team')
            ->get();
        foreach ($fgcs as $fgc) {
            $teams->push($fgc->team ?? new Team());
        }

        return $teams;

    }

    public function getFighters()
    {

        if ($this->championship->category->isTeam()) {
            $fighters = $this->teamsWithNull();
        } else {
            $fighters = $this->competitorsWithNull();
        }

        if (sizeof($fighters) == 0) {
            $treeGen = new TreeGen($this->championship, null, null);
            $fighters = $treeGen->createByeGroup(2);
        }
        return $fighters;
    }

    public static function getBaseNumGroups($initialGroupId, $numGroups, $numRound): int
    {
//        dump("New:". $initialGroupId . " " . $numGroups . " " . $numRound);
        // numGroups = 4, numRound = 2;
        $parentId = $initialGroupId;

        for ($i = 1; $i <= $numRound; $i++) {
            $parentId += $numGroups / $numRound;
//            dump("parent_id Temporal:" . $parentId);

        }

        return $parentId;
    }
}
