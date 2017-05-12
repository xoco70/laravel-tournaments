<?php

namespace Xoco70\KendoTournaments;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Xoco70\KendoTournaments\Contracts\TreeGenerable;
use Xoco70\KendoTournaments\Exceptions\TreeGenerationException;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Tournament;
use Xoco70\KendoTournaments\TreeGen\DirectEliminationCompetitorTreeGen;
use Xoco70\KendoTournaments\TreeGen\DirectEliminationTeamTreeGen;
use Xoco70\KendoTournaments\TreeGen\PlayOffCompetitorTreeGen;
use Xoco70\KendoTournaments\TreeGen\PlayOffTeamTreeGen;
use Xoco70\KendoTournaments\TreeGen\TreeGen;

class TreeController extends Controller
{
    /**
     * Display a listing of trees.
     *
     * @param Request $request
     *
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
            ->with('tournament', $tournament)
            ->with('settings', $tournament->championships[0]->setting);
    }

    /**
     * Build Tree.
     *
     * @param Request $request
     * @param Championship $championship
     *
     * @return \Illuminate\Http\Response|string
     */
    public function store(Request $request, Championship $championship)
    {
        DB::table('fight')->delete();
        DB::table('fighters_groups')->delete();
        DB::table('fighters_group_competitor')->delete();
        DB::table('fighters_group_team')->delete();
        DB::table('competitor')->delete();
        DB::table('users')->where('id', '<>', 1)->delete();
        $numFighters = $request->numFighters;

        $users = factory(User::class, (int)$numFighters)->create();

        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'championship_id' => $championship->id,
                'user_id' => $user->id,
                'confirmed' => 1,
                'short_id' => $user->id
            ]);
        }
        $championship->settings = ChampionshipSettings::createOrUpdate($request, $championship);
        $generation = $championship->chooseGenerationStrategy();

        $fighterToUpdate = null;
        try {
            $generation->run();
            FightersGroup::generateFights($championship);
            // For Now, We don't generate fights when Preliminary
            if ($championship->isDirectEliminationType() && !$championship->hasPreliminary()) {
                FightersGroup::generateNextRoundsFights($championship);
            }
        } catch (TreeGenerationException $e) {
            redirect()->back()
                ->withErrors([$numFighters . "-" . $e->getMessage()]);
        }
        return redirect()->back()
            ->with('numFighters', $numFighters)
            ->with('hasPreliminary', $championship->settings->hasPreliminary)
            ->with(['success', "Success"]);
    }


}
