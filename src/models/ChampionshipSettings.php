<?php

namespace Xoco70\LaravelTournaments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
