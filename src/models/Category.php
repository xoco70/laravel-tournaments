<?php

namespace Xoco70\LaravelTournaments\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed team
 * @property mixed gender
 * @property mixed ageCategory
 * @property mixed ageMin
 * @property mixed ageMax
 * @property mixed gradeMin
 * @property mixed gradeMax
 * @property mixed isTeam
 */
class Category extends Model
{
    protected $table = 'category';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'name',
        'alias',
        'gender',
        'isTeam',
        'ageCategory',
        'ageMin',
        'ageMax',
        'gradeCategory',
        'gradeMin',
        'gradeMax',
    ];

    public function getAliasString(){
        return $this->alias;
    }

    public function getNameAttribute($name)
    {

        return trans($name);
    }

    public function getGradeAttribute($grade)
    {
        return trans($grade);
    }

    public function getAgeCategoryAttribute($ageCategory)
    {
        return trans($ageCategory);
    }

    public function tournaments()
    {
        return $this->belongsToMany('App\Tournament');
    }

    public function settings()
    {
        return $this->hasOne('App\ChampionshipSettings');
    }
    public function championship()
    {
        return $this->hasMany(Championship::class);
    }

    public function isTeam()
    {
        return $this->isTeam;
    }

    public function isForMen()
    {
        return $this->gender == "M";
    }

    public function isForWomen()
    {
        return $this->gender == "F";
    }

    public function isMixt()
    {
        return $this->gender == "X";
    }
}
