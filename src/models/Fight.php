<?php

namespace Xoco70\LaravelTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Fight extends Model
{
    /**
     * Fight constructor.
     *
     * @param int $userId1
     * @param int $userId2
     */
    public function __construct($userId1 = null, $userId2 = null)
    {
        parent::__construct();
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
     *
     * @return Collection
     */
    protected static function getFightersWithByes(FightersGroup $group)
    {
        if ($group == null) {
            return;
        }
        $fighters = $group->getFightersWithBye();
        $fighterType = $group->getFighterType();
        if (count($fighters) == 0) {
            $fighters->push(new $fighterType());
            $fighters->push(new $fighterType());
        } elseif (count($fighters) % 2 != 0) {
            $fighters->push(new $fighterType());
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

    /**
     * @param $numFighter
     * @param $attr
     *
     * @return null|string
     */
    public function getFighterAttr($numFighter, $attr)
    {
        $isTeam = $this->group->championship->category->isTeam;
        if ($isTeam) {
            $teamToUpdate = 'team'.$numFighter;

            return $this->$teamToUpdate == null ? '' : $this->$teamToUpdate->$attr;
        }
        $competitorToUpdate = 'competitor'.$numFighter;
        if ($attr == 'name') {
            return $this->$competitorToUpdate == null
                ? 'BYE'
                : $this->$competitorToUpdate->user->firstname.' '.$this->$competitorToUpdate->user->lastname;
        } elseif ($attr == 'short_id') {
            return $this->$competitorToUpdate == null ? '' : $this->$competitorToUpdate->short_id;
        }
    }

    /**
     * @return bool
     */
    public function shouldBeInFightList()
    {
        if ($this->belongsToFirstRound() && $this->dontHave2Fighters()) {
            return false;
        }
        if (!$this->belongsToFirstRound() && $this->dontHave0Fighters()) {
            return true;
        }
        if ($this->has2Fighters()) {
            return true;
        }
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
     * return true if fight has 2 fighters ( No BYE ).
     *
     * @return bool
     */
    public function has2Fighters(): bool
    {
        return $this->c1 != null && $this->c2 != null;
    }

    /**
     * @return bool
     */
    private function belongsToFirstRound()
    {
        $firstRoundFights = $this->group->championship->firstRoundFights->pluck('id')->toArray();
        if (in_array($this->id, $firstRoundFights)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function dontHave2Fighters()
    {
        return $this->c1 == null || $this->c2 == null;
    }

    /**
     * @return bool
     */
    private function dontHave0Fighters()
    {
        return $this->c1 != null || $this->c2 != null;
    }

    /**
     * @param Championship $championship
     */
    public static function generateFightsId(Championship $championship)
    {
        $order = 1;
        foreach ($championship->fights as $fight) {
            $order = $fight->updateShortId($order);
        }
    }

    /**
     * @param $order
     *
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
