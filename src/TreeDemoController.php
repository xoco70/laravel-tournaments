<?php

namespace Xoco70\KendoTournaments;

use App\Http\Controllers\Controller;
use Xoco70\KendoTournaments\Models\Tournament;

class TreeDemoController extends Controller
{

    public function index()
    {
        $tournament = Tournament::with(
            'competitors',
            'championshipSettings',
            'championships.settings',
            'championships.category')->first();

        return view('kendo-tournaments::tree_demo.index')
            ->with('tournament', $tournament);
    }

}