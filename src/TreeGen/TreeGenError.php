<?php


namespace App\TreeGen;


use App\Championship;
use App\ChampionshipSettings;
use App\Contracts\TreeGenerable;
use App\Tree;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class TreeGenError 
{
    protected $status, $error;

    /**
     * TreeGenError constructor.
     * @param $status
     * @param $error
     */
    public function __construct($status, $error)
    {
        $this->status = $status;
        $this->error = $error;
    }


}