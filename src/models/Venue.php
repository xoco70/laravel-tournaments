<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venue';
    public $timestamps = true;
    protected $fillable = [
        'venue_name',
        'address',
        'details',
        'city',
        'CP',
        'state',
        'latitude',
        'longitude',
    ];
}
