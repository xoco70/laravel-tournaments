<?php

namespace Xoco70\KendoTournaments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xoco70\KendoTournaments\Exceptions\TreeGenerationException;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Tournament;
use Xoco70\KendoTournaments\Models\Round;
use Xoco70\KendoTournaments\TreeGen\TreeGen;

class TreeController extends Controller
{
    /**
     * Display a listing of trees.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tournament = Tournament::with(
            'competitors',
            'championshipSettings',
            'championships.settings',
            'championships.category')->first();

        return view('kendo-tournaments::tree.index')
            ->with('tournament', $tournament);
    }

    /**
     * Build Tree
     *
     * @param Request $request
     * @param Championship $championship
     * @return \Illuminate\Http\Response|string
     */
    public function store(Request $request, Championship $championship)
    {
        // Store Championship Settings

        $tournament = Tournament::with(
            'competitors',
            'championshipSettings'
//            'championships.settings',
//            'championships.category',
//            'championships.teams',
//            'championships.users'
            )->first();

        $championship = Championship::with('teams','users','category','settings')->find($championship->id);

        $settings = ChampionshipSettings::createOrUpdate($request, $championship);


        $generation = new TreeGen($championship, null, $settings);
        $generation->championship = $championship;
        try {

            $tree = $generation->run();
            $championship->tree = $tree;


            Round::generateFights($tree, $settings, $championship);

        } catch (TreeGenerationException $e) {
            return view('kendo-tournaments::tree.index')
                ->with('tournament', $tournament)
                ->with('error', "Error Generating Tree");
        }

        return view('kendo-tournaments::tree.index')
            ->with('tournament', $tournament)
            ->with('message', "Tree Generated");
    }


}
