<?php

namespace Xoco70\LaravelTournaments\TreeGen;

class TreeGenError
{
    protected $status;
    protected $error;

    /**
     * TreeGenError constructor.
     *
     * @param $status
     * @param $error
     */
    public function __construct($status, $error)
    {
        $this->status = $status;
        $this->error = $error;
    }
}
