<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Round;
use Xoco70\KendoTournaments\Models\Tournament;

class PreliminaryTreeTest extends TestCase
{
//    use DatabaseTransactions;

    protected $root;
    protected $tournament, $championship, $settings, $users;


    /** @test */
    public function check_number_of_row_when_generating_tournament()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
        $numGroupsExpected = [0, 1, 1, 2, 2, 2, 4, 4, 4, 4, 4, 4, 8, 8, 8, 8, 8, 8];
        $numAreas = [1, 2, 4];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
//                $this->initialize($numArea, $numCompetitors);
                $this->clickGenerate($numArea, $numCompetitors, $hasPreliminary = 1, $hasRoundRobin = 0 );
                $this->check_assertion($numArea, $this->championship, $numCompetitors, $numGroupsExpected);
            }
        }
    }

    /**
     * @param $users
     */
    public function makeCompetitors($championship, $users)
    {
        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'user_id' => $user->id,
                'championship_id' => $championship->id,
                'confirmed' => 1,]);
        }
    }

    public function clickGenerate($numAreas, $numCompetitors, $hasRoundRobin)
    {
        $this->visit('/kendo-tournaments')
            ->select($numAreas, 'fightingAreas')
            ->select($hasRoundRobin, 'treeType')
            ->press('save');
    }

    /**
     * @param $numArea
     * @param $championship
     * @param $numCompetitors
     * @param $numGroupsExpected
     */
    private function check_assertion($numArea, $championship, $numCompetitors, $numGroupsExpected)
    {
        for ($area = 1; $area <= $numArea; $area++) {
            $count = Round::where('championship_id', $this->championship->id)
                ->where('area', $area)->count();

            if ((int)($numCompetitors / $numArea) <= 1) {
                $this->assertTrue($count == 0);
            } else {
                $expected = (int)($numGroupsExpected[$numCompetitors - 1] / $numArea);

                if ($count != $expected) {
                    dd(['Type' => 'RoundRobin'],
                        ['NumCompetitors' => $numCompetitors],
                        ['NumArea' => $numArea],
                        ['Real' => $count],
                        ['Excepted' => $expected],
                        ['numGroupsExpected[' . ($numCompetitors - 1) . ']' => $numGroupsExpected[$numCompetitors - 1] . ' / ' . $numArea]);
                }
                $this->assertTrue($count == $expected);
            }
        }
    }

    /**
     * @param $numArea
     * @param $numCompetitors
     */
    private function initialize($numArea, $numCompetitors)
    {
        $this->tournament = factory(Tournament::class)->create();

        $this->championship = factory(Championship::class)->create([
            'tournament_id' => $this->tournament->id,
            'category_id' => 1,
        ]);

        $this->settings = factory(ChampionshipSettings::class)->create([
            'championship_id' => $this->championship->id,
            'hasPreliminary' => 1,
            'teamSize' => null,
            'fightingAreas' => $numArea,
            'preliminaryWinner' => 1,
            'preliminaryGroupSize' => 3,
        ]);

        $this->users = factory(User::class, $numCompetitors)->create();
        $this->makeCompetitors($this->championship, $this->users);
    }
}
