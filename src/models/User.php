<?php

namespace Xoco70\LaravelTournaments\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * @property  mixed name
 * @property  mixed email
 * @property  mixed password
 * @property bool verified
 * @property mixed token
 * @property  mixed clearPassword
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    protected $dates = ['created_at', 'updated_at'];
//    protected $appends = [''];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'email'
            ]
        ];
    }

//    protected $sluggable = [
//        'build_from' => 'email',
//        'save_to' => 'slug',
//    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'password_confirmation'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get all user's created (owned) tournmanents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tournaments()
    {
        return $this->hasMany('App\Tournament');
    }


    public function categories()
    {
        return $this->belongsToMany('App\Category', 'competitor', 'user_id', 'championship_id');
    }

    public function championships()
    {
        return $this->belongsToMany(Championship::class, 'competitor')
            ->withTimestamps();
    }


    public function competitors()
    {
        return $this->hasMany(Competitor::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
