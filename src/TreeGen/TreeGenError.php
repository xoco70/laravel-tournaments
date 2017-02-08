<?php


namespace Xoco70\KendoTournaments\TreeGen;

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