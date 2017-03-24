<?php

namespace Xoco70\KendoTournaments\Models;


use Illuminate\Database\Eloquent\Model;

class FighterGroupTeam extends Model
{
    protected $table = 'fighters_group_team';
    public $timestamps = true;


    public function group(){
        return $this->belongsTo(FightersGroup::class);
    }

    public function team(){
        return $this->belongsTo(Team::class);
    }
}