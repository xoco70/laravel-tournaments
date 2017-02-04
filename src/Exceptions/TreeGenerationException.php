<?php


namespace App\Exceptions;


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