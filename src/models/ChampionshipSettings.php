<?php

namespace Xoco70\KendoTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class ChampionshipSettings extends Model
{
    use SoftDeletes;
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = 'championship_settings';
    public $timestamps = true;
    protected $guarded = ['id'];

    const MIN_COMPETITORS_BY_AREA = 2,
        PRELIMINARY_GROUP_SIZE = [3 => 3, 4 => 4, 5 => 5],
        PRELIMINARY_WINNERS = [1 => 1], // , 2 => 2, 3 => 3
        TEAM_SIZE = [2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10],
        TEAM_RESERVE = [1 => 1, 2 => 2, 3 => 3, 4 => 4],
        LIMIT_BY_ENTITY = [0 => '-', 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10];

    /**
     * A Setting belongs to a Championship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * @param Request $request
     * @param Championship $championship
     *
     * @return ChampionshipSettings
     */
    public static function createOrUpdate(Request $request, Championship $championship): ChampionshipSettings
    {
        $request->request->add(['championship_id' => $championship->id]);
        $arrSettings = $request->except('_token', 'numFighters');
        $settings = static::where(['championship_id' => $championship->id])->first();
        if ($settings == null) {
            $settings = new self($arrSettings);
        } else {
            $settings->fill($arrSettings);
        }
        $settings->save();

        return $settings;
    }
}
