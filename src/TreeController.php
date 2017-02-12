<?php

namespace Xoco70\KendoTournaments;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Xoco70\KendoTournaments\Exceptions\TreeGenerationException;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Fight;
use Xoco70\KendoTournaments\Models\Tournament;
use Xoco70\KendoTournaments\Models\Round;
use Xoco70\KendoTournaments\TreeGen\TreeGen;
use Faker\Factory as Faker;

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

//        DB::table('competitor')->delete();

        $tournament = Tournament::with(
            'competitors',
            'championshipSettings'
        )->first();

        $championship = Championship::with('teams', 'users', 'category', 'settings')->find($championship->id);

        $numFighters = $request->numFighters;

        $users = factory(User::class, (int)$numFighters)->create();

        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'championship_id' => $championship->id,
                'user_id' => $user->id,
                'confirmed' => 1,
            ]);
        }

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
