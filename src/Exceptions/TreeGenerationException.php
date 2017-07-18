<?php

namespace Xoco70\KendoTournaments\Exceptions;

use Illuminate\Support\Facades\Config;

class TreeGenerationException extends \Exception
{
    public $message;

    /**
     * TreeGenerationException constructor.
     *
     * @param string
     */
    public function __construct($msg)
    {
        $this->message = $msg;
    }
}
