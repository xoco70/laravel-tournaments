<?php

namespace Xoco70\KendoTournaments\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NodeTrait;
use Xoco70\KendoTournaments\TreeGen\DirectEliminationTreeGen;
use Xoco70\KendoTournaments\TreeGen\TreeGen;

class FightersGroup extends Model
{
    protected $table = 'fighters_groups';
    public $timestamps = true;
    protected $guarded = ['id'];

    use NodeTrait;

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

    public function fightersWithBye()
    {
        if ($this->championship->category->isTeam()) {
            return $this->teamsWithBye();
        }
        return $this->competitorsWithBye();
    }

    public function fighters()
    {
        if ($this->championship->category->isTeam()) {
            return $this->teams;
        }
        return $this->competitors;
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
     *
     * @return Collection
     */
    public function competitorsWithBye(): Collection
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


    public function teamsWithBye(): Collection
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

    public function getFighters(): Collection
    {
        if ($this->championship->category->isTeam()) {
            return $this->teamsWithBye();
        }
        return $this->competitorsWithBye();

    }

    public static function getBaseNumGroups($initialGroupId, $numGroups, $numRound): int
    {
        $parentId = $initialGroupId;

        for ($i = 1; $i <= $numRound; $i++) {
            $parentId += $numGroups / $numRound;
        }

        return $parentId;
    }

    /**
     * @return string
     */
    public function getFighterType()
    {
        if ($this->championship->category->isTeam()) {
            return Team::class;
        }
        return Competitor::class;
    }

    /**
     * Check if we are able to fill the parent fight or not
     * If one of the children has c1 x c2, then we must wait to fill parent
     *
     * @return bool
     */
    public function hasDeterminedParent()
    {
        // There is more than 1 fight, should be Preliminary
        if (sizeof($this->fighters()) > 1){
            return false;
        }
        foreach ($this->children as $child) {
            if (sizeof($child->fighters()) > 1) return false;
        }
        return true;

    }


    /**
     * In the original fight ( child ) return the field that contains data to copy to parent
     * @return int
     */
    public function getValueToUpdate()
    {
        if ($this->championship->category->isTeam()) {
            return $this->teams->map->id[0];
        }
        return $this->competitors->map->id[0];
    }

    /**
     * Returns the parent field that need to be updated
     * @return null|string
     */
    public function getParentFighterToUpdate($keyGroup)
    {
        if (intval($keyGroup % 2) == 0) {
            return "c1";
        }
        if (intval($keyGroup % 2) == 1) {
            return "c2";
        }

        return null;
    }
}
