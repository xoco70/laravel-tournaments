<?php

namespace Xoco70\LaravelTournaments\Exceptions;

class TreeGenerationException extends \Exception
{
    public $message;
    public $code;

    /**
     * TreeGenerationException constructor.
     *
     * @param string
     */
    public function __construct($msg, $code)
    {
        $this->message = $msg;
        $this->code = $code;
    }
}
