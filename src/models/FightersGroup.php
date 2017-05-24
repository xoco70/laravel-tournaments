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
     * @param Championship $championship
     * @param $fightsByRound
     */
    private static function updateParentFight(Championship $championship, $fightsByRound)
    {
        foreach ($fightsByRound as $fight) {
            $parentGroup = $fight->group->parent;
            if ($parentGroup == null) break;
            $parentFight = $parentGroup->fights->get(0); //TODO This Might change when extending to Preliminary

            // IN this $fight, is c1 or c2 has the info?
            if ($championship->isDirectEliminationType()) {
                // determine whether c1 or c2 must be updated
                self::chooseAndUpdateParentFight($fight, $parentFight);
            }
        }
    }

    /**
     * @param $fight
     * @param $parentFight
     */
    private static function chooseAndUpdateParentFight($fight, $parentFight)
    {
        $fighterToUpdate = $fight->getParentFighterToUpdate();
        $valueToUpdate = $fight->getValueToUpdate();
        // we need to know if the child has empty fighters, is this BYE or undetermined
        if ($fight->hasDeterminedParent() && $valueToUpdate != null) {
            $parentFight->$fighterToUpdate = $fight->$valueToUpdate;
            $parentFight->save();
        }
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

    public function fighters()
    {
        if ($this->championship->category->isTeam()) {
            return $this->teamsWithNull();
        }
        return $this->competitorsWithNull();
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
     * Generate First Round Fights
     * @param Championship $championship
     */
    public static function generateFights(Championship $championship)
    {
        $settings = $championship->getSettings();
        // Delete previous fight for this championship

        $arrGroupsId = $championship->fightersGroups()->get()->pluck('id');

        Fight::destroy($arrGroupsId);

        if ($settings->hasPreliminary && $settings->preliminaryGroupSize == 3) {
            for ($numGroup = 1; $numGroup <= $settings->preliminaryGroupSize; $numGroup++) {
                PreliminaryFight::saveFights($championship->fightersGroups()->get(), $numGroup);
            }
        } else {
            DirectEliminationFight::saveFights($championship);
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
     *
     * @return Collection
     */
    public function competitorsWithNull(): Collection
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


    public function teamsWithNull(): Collection
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
            return $this->teamsWithNull();
        }
        return $this->competitorsWithNull();

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
     *
     * @param Championship $championship
     */
    public static function generateNextRoundsFights(Championship $championship)
    {
        $championship = $championship->withCount('teams', 'competitors')->first();
        $fightersCount = $championship->competitors_count + $championship->teams_count;
        $maxRounds = intval(ceil(log($fightersCount, 2)));
        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
            $fightsByRound = $championship->fightsByRound($numRound)->with('group.parent', 'group.children')->get();
            self::updateParentFight($championship, $fightsByRound);
        }

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
}
