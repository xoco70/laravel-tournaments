<?php


namespace Xoco70\KendoTournaments\Exceptions;



use Illuminate\Support\Facades\Config;

class TreeGenerationException extends \Exception
{
    public $message;
    /**
     * TreeGenerationException constructor.
     * @param string
     */
    public function __construct()
    {
        $this->message = trans('msg.min_competitor_required', ['number' => Config::get('kendo-tournaments.MIN_COMPETITORS_X_AREA')]);
    }
}