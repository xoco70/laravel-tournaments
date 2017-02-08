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

    /**
     * A Setting belongs to a Championship
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * @param Request $request
     * @param Championship $championship
     * @return ChampionshipSettings
     */
    public static function createOrUpdate(Request $request, Championship $championship): ChampionshipSettings
    {
        $request->request->add(['championship_id' => $championship->id]);
        $arrSettings = $request->except('_token');
        $settings = static::where(['championship_id' => $championship->id])->first();
        if ($settings == null) {
            $settings = new ChampionshipSettings($arrSettings);
        } else {
            $settings->fill($arrSettings);
        }
        $settings->save();
        return $settings;
    }
}
