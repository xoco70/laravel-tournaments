<?php

namespace Xoco70\LaravelTournaments;

use App\Http\Controllers\Controller;
use Xoco70\LaravelTournaments\Models\Tournament;

class TreeDemoController extends Controller
{

    public function index()
    {
        $tournament = Tournament::firstOrFail();
        return view('laravel-tournaments::tree_demo.index')
            ->with('tournament', $tournament);
    }

}