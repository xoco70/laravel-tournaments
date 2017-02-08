<?php

namespace Xoco70\LaravelTournaments;

use App\Http\Controllers\Controller;
use Xoco70\LaravelTournaments\Models\Tournament;

class TreeDemoController extends Controller
{

    public function index()
    {
        $tournament = Tournament::with(
            'competitors',
            'championshipSettings',
            'championships.settings',
            'championships.category')->first();

        return view('laravel-tournaments::tree_demo.index')
            ->with('tournament', $tournament);
    }

}