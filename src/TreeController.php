<?php

namespace Xoco70\KendoTournaments;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Xoco70\KendoTournaments\Exceptions\TreeGenerationException;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Fight;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Team;
use Xoco70\KendoTournaments\Models\Tournament;

class TreeController extends Controller
{
    /**
     * Display a listing of trees.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tournament = Tournament::with(
            'competitors',
            'championshipSettings',
            'championships.settings',
            'championships.category')->first();

        return view('kendo-tournaments::tree.index')
            ->with('tournament', $tournament)
            ->with('championship', $tournament->championships[0])
            ->with('settings', $tournament->championships[0]->setting);
    }

    /**
     * Build Tree.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|string
     */
    public function store(Request $request, $championshipId)
    {
        $this->deleteEverything();
        $numFighters = $request->numFighters;
        $isTeam = $request->isTeam ?? 0;
        $championship = $this->provisionObjects($request, $isTeam, $numFighters);
        $generation = $championship->chooseGenerationStrategy();
        try {
            $generation->run();
        } catch (TreeGenerationException $e) {
            redirect()->back()
                ->withErrors([$numFighters . "-" . $e->getMessage()]);
        }

        $tournament = Tournament::with(
            'competitors',
            'championshipSettings',
            'championships.settings',
            'championships.category')->first();

        return view('kendo-tournaments::tree.index',
            compact('tournament', 'championship', 'numFighters', 'isTeam'));

    }

    private function deleteEverything()
    {
        DB::table('fight')->delete();
        DB::table('fighters_groups')->delete();
        DB::table('fighters_group_competitor')->delete();
        DB::table('fighters_group_team')->delete();
        DB::table('competitor')->delete();
        DB::table('team')->delete();
        DB::table('users')->where('id', '<>', 1)->delete();
    }

    /**
     * @param Request $request
     * @param $isTeam
     * @param $numFighters
     * @return Championship
     */
    protected function provisionObjects(Request $request, $isTeam, $numFighters)
    {
        if ($isTeam) {
            $championship = Championship::find(2);
            factory(Team::class, (int)$numFighters)->create(['championship_id' => $championship->id]);
        } else {
            $championship = Championship::find(1);
            $users = factory(User::class, (int)$numFighters)->create();
            foreach ($users as $user) {
                factory(Competitor::class)->create(
                    ['championship_id' => $championship->id,
                        'user_id' => $user->id,
                        'confirmed' => 1,
                        'short_id' => $user->id
                    ]
                );
            }
        }
        $championship->settings = ChampionshipSettings::createOrUpdate($request, $championship);
        return $championship;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $championshipId)
    {
        $numFight = 0;
//        $championshipId = $request->championshipId;
//        $championship = Championship::find($request->championshipId);
        $groups = FightersGroup::with('fights')
            ->where('championship_id', $championshipId)
            ->where('round','>',1)
            ->get();
        $fights = $request->fights;

        foreach ($groups as $group) {
            foreach ($group->fights as $fight) {
                // Find the fight in array, and update order
                $fight->c1 = $fights[$numFight++];
                $fight->c2 = $fights[$numFight++];
                $fight->save();
            }
        }
        return back();
    }


}
