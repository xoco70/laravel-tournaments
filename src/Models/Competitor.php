<?php

namespace Xoco70\LaravelTournaments\Models;

use Illuminate\Foundation\Auth\User;

class Competitor extends Fighter
{
    protected $DATES = ['created_at', 'updated_at'];

    protected $table = 'competitor';
    public $timestamps = true;
    protected $fillable = [
        'tournament_category_id',
        'user_id',
        'confirmed',
    ];

    /**
     * Get User from Competitor.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return null|string
     */
    public function getNameAttribute()
    {
        return $this->defaultName() ?? $this->user->name;
    }

    /**
     * @return null|string
     */
    public function getFullName() //TODO Should remove get prefix
    {
        return $this->defaultName() ?? $this->user->firstname.' '.$this->user->lastname;
    }

    /**
     * @return null|string
     */
    private function defaultName()
    {
        if ($this == null || $this->user == null) {
            return '';
        }
    }
}
