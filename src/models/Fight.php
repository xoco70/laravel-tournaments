<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Fight extends Model
{
    /**
     * Fight constructor.
     * @param int $userId1
     * @param int $userId2
     */
    public function __construct($userId1 = null, $userId2 = null)
    {
        $this->c1 = $userId1;
        $this->c2 = $userId2;
    }

    protected $table = 'fight';
    public $timestamps = true;

    protected $fillable = [
        'group_id',
        'c1',
        'c2',
    ];


    /**
     * Get First Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(FightersGroup::class, 'fighters_group_id');
    }

    /**
     * @param FightersGroup|null $group
     * @return Collection
     * @internal param Championship $championship
     *
     */
    protected static function getActorsToFights(FightersGroup $group)
    {
        if ($group == null) return null;
        $fighters = $group->getFighters();
        $fighterType = $group->getFighterType();
        if (sizeof($fighters) == 0) {
            $fighters->push(new $fighterType);
            $fighters->push(new $fighterType);
        } else if (count($fighters) % 2 != 0) {
            $fighters->push(new $fighterType);
        }

        return $fighters;
    }

    /**
     * Get First Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competitor1()
    {
        return $this->belongsTo(Competitor::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function competitor2()
    {
        return $this->belongsTo(Competitor::class, 'c2', 'id');
    }

    /**
     * Get First Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team1()
    {
        return $this->belongsTo(Team::class, 'c1', 'id');
    }

    /**
     * Get Second Fighter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team2()
    {
        return $this->belongsTo(Team::class, 'c2', 'id');
    }


    public function getFighterAttr($numFighter, $attr)
    {
        $isTeam = $this->group->championship->category->isTeam;
        if ($isTeam) {
            $teamToUpdate = 'team' . $numFighter;
            return $this->$teamToUpdate == null ? '' : $this->$teamToUpdate->$attr;
        }
        $competitorToUpdate = 'competitor' . $numFighter;
        if ($attr == 'name') {
            return $this->$competitorToUpdate == null
                ? 'BYE'
                : $this->$competitorToUpdate->user->firstname . " " . $this->$competitorToUpdate->user->lastname;
        } elseif ($attr == 'short_id') {
            return $this->$competitorToUpdate == null ? '' : $this->$competitorToUpdate->short_id;
        }
        return null;
    }

    /**
     * Update parent Fight
     * @param $fighterToUpdate
     * @param $fight
     */
    public function updateParentFight($fighterToUpdate, $fight)
    {
        if ($fight != null) {
            if (($fight->c1 != null || $fight->c2 == null)) {
                $this->$fighterToUpdate = $fight->c1;
            }
            if ($fight->c1 == null || $fight->c2 != null) {
                $this->$fighterToUpdate = $fight->c2;
            }
            if ($fight->dontHave2Fighters()) {
                $this->$fighterToUpdate = null;
            }
        }

    }

    /**
     * Returns the parent field that need to be updated
     * @return null|string
     */
    public function getParentFighterToUpdate()
    {
        $childrenGroup = $this->group->parent->children;
        foreach ($childrenGroup as $key => $children) {
            $childFight = $children->fights->get(0);
            if ($childFight->id == $this->id) {
                if ($key % 2 == 0) {
                    return "c1";
                }
                if ($key % 2 == 1) {
                    return "c2";
                }
            }
        }
        return null;
    }

    /**
     * In the original fight ( child ) return the field that contains data to copy to parent
     * @return null|string
     */
    public function getValueToUpdate()
    {
        if ($this->c1 != null && $this->c2 != null) {
            return null;
        }
        if ($this->c1 != null) {
            return "c1";
        }
        if ($this->c2 != null) {
            return "c2";
        }
        return null;
    }

    /**
     * Check if we are able to fill the parent fight or not
     * If one of the children has c1 x c2, then we must wait to fill parent
     *
     * @return bool
     */
    public function hasDeterminedParent()
    {
        if ($this->has2Fighters()) return true;
        foreach ($this->group->children as $child) {
            $fight = $child->fights->get(0);
            if ($fight->has2Fighters()) return false;
        }
        return true;
    }

    public function shouldBeInFightList()
    {
        if ($this->belongsToFirstRound() && $this->dontHave2Fighters()) return false;
        if ($this->has2Fighters()) return true;
        // We aint in the first round, and there is 1 or 0 competitor
        // We check children, and see :
        // if there is 2  - 2 fighters -> undetermine, we cannot add it to fight list
        // if there is 2  - 1 fighters -> undetermine, we cannot add it to fight list
        // if there is 2  - 0 fighters -> undetermine, we cannot add it to fight list
        // if there is 1  - 2 fighters -> undetermine, we cannot add it to fight list
        // if there is 1  - 1 fighters -> fight should have 2 fighters, undetermines
        // if there is 1  - 0 fighters -> determined, fight should not be in the list
        // if there is 0  - 1 fighters -> determined, fight should not be in the list
        // So anyway, we should return false
        return false;
    }

    /**
     * return true if fight has 2 fighters ( No BYE )
     * @return bool
     */
    private function has2Fighters(): bool
    {
        return $this->c1 != null && $this->c2 != null;
    }

    private function belongsToFirstRound()
    {
        $firstRoundFights = $this->group->championship->firstRoundFights->pluck('id')->toArray();
        if (in_array($this->id, $firstRoundFights)) return true;
        return false;
    }

    private function dontHave2Fighters() // 1 or 0
    {
        return $this->c1 == null || $this->c2 == null;
    }


    public static function generateFightsId($championship)
    {
        $order = 1;
        foreach ($championship->fights as $fight) {
            $order = $fight->updateShortId($order);
        }
    }

    /**
     * @param $order
     * @return int
     */
    public function updateShortId($order)
    {
        if ($this->shouldBeInFightList()) {
            $this->short_id = $order;
            $this->save();
            return ++$order;
        }
        return $order;
    }

}