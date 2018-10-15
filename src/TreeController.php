<?php

namespace Xoco70\LaravelTournaments;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\Exceptions\TreeGenerationException;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Models\Team;
use Xoco70\LaravelTournaments\Models\Tournament;

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
            'championships.settings',
            'championships.category')->first();

        return view('laravel-tournaments::tree.index')
            ->with('tournament', $tournament);
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
                ->withErrors($e->getMessage());
        }

        return back()
            ->with('numFighters', $numFighters)
            ->with('isTeam', $isTeam);
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
     *
     * @return Championship
     */
    protected function provisionObjects(Request $request, $isTeam, $numFighters)
    {
        if ($isTeam) {
            $championship = Championship::find(2);
            factory(Team::class, (int) $numFighters)->create(['championship_id' => $championship->id]);
        } else {
            $championship = Championship::find(1);
            $users = factory(\Illuminate\Foundation\Auth\User::class, (int) $numFighters)->create();
            foreach ($users as $user) {
                factory(Competitor::class)->create(
                    ['championship_id' => $championship->id,
                        'user_id'      => $user->id,
                        'confirmed'    => 1,
                        'short_id'     => $user->id,
                    ]
                );
            }
        }
        $championship->settings = ChampionshipSettings::createOrUpdate($request, $championship);

        return $championship;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Championship $championship)
    {
        $numFighter = 0;
        $query = FightersGroup::with('fights')
            ->where('championship_id', $championship->id);

        $fighters = $request->singleElimination_fighters;
        $scores = $request->score;
        if ($championship->hasPreliminary()) {
            $query = $query->where('round', '>', 1);
            $fighters = $request->preliminary_fighters;
        }
        $groups = $query->get();

        foreach ($groups as $group) {
            foreach ($group->fights as $fight) {
                $fight->c1 = $fighters[$numFighter];
                $fight->winner_id = $this->getWinnerId($fighters, $scores, $numFighter);
                $numFighter++;

                $fight->c2 = $fighters[$numFighter];
                if ($fight->winner_id == null) {
                    $fight->winner_id = $this->getWinnerId($fighters, $scores, $numFighter);
                }
                $numFighter++;
                $fight->save();
            }
        }

        return back();
    }

    public function getWinnerId($fighters, $scores, $numFighter)
    {
        return $scores[$numFighter] != null ? $fighters[$numFighter] : null;
    }
}
