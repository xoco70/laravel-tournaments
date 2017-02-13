<?php

namespace Xoco70\KendoTournaments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xoco70\KendoTournaments\Models\Tournament;

class FightController extends Controller
{
    /**
     * Display a listing of the fights.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tournament = Tournament::with('championships.tree.fights')
            ->where('slug', $request->tournament)
            ->first();
        return view('fights.index', compact('tournament'));

    }
}
