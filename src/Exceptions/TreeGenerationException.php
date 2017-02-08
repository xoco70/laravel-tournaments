<?php


namespace Xoco70\KendoTournaments\Exceptions;


class TreeGenerationException extends \Exception
{
    public $message;
    /**
     * TreeGenerationException constructor.
     * @param string
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
}