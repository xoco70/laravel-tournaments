<?php

namespace Xoco70\LaravelTournaments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Competitor extends Model
{
    use SoftDeletes;
    protected $DATES = ['created_at', 'updated_at', 'deleted_at'];


    protected $table = 'competitor';
    public $timestamps = true;
    protected $fillable = [
        "tournament_category_id",
        "user_id",
        "confirmed",
    ];


    /**
     * Get the Competitor's Championship
     * @param $ctId
     * @return Collection
     */
    public function championship($ctId)
    {
        //TODO Surely I could Refactor it to Eloquent - Should Debug that. $ctId <> $championshipId ???
        $competitor = Competitor::where('championship_id', $ctId)->first();
        $championshipId = $competitor->championship_id;
        $championship = Championship::find($championshipId);

        return $championship;
    }

    /**
     * Not sure I use it, I could use $competitor->championship->category
     * @param $ctuId
     * @return mixed
     */
    public function category($ctuId)
    {
        $championship = $this->championship($ctuId);
        $categoryId = $championship->category_id;
        $cat = Category::find($categoryId);
        return $cat;
    }


    /**
     * Get the tournament where Competitors is competing
     * @param $ctuId
     * @return mixed
     */
    public function tournament($ctuId)
    {
        $tc = $this->championship($ctuId);
        $tourmanentId = $tc->tournament_id;
        $tour = Tournament::findOrNew($tourmanentId);
        return $tour;
    }

    /**
     * Get User from Competitor
     * @return mixed
     */
    public function user()
    {
        return self::find($this->user_id);
    }


}
