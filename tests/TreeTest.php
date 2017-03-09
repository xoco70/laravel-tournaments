<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Tournament;

class TreeTest extends TestCase
{
    use DatabaseTransactions;

    protected $root;
    protected $tournament, $championship, $settings, $users;


    public function setUp()
    {
        parent::setUp();
        $this->tournament = $tournament = Tournament::with(
            'competitors',
            'championshipSettings'
        )->first();


        $this->championship = Championship::with('teams', 'users', 'category', 'settings')->find($tournament->championships[0]->id);
    }

    /** @test */
    public function check_number_of_row_when_generating_preliminary_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8];
        $numGroupsExpected = [0, 1, 1, 2, 2, 2, 4, 4];
        $numAreas = [1, 2, 4];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->clickGenerate($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasRoundRobin = false, $hasPreliminary = true);
                $this->checkAssertion($numArea, $numCompetitors, $numGroupsExpected);
            }
        }
    }

    /** @test */
    public function check_number_of_row_when_generating_round_robin_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 1, 1, 1, 1]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numFightsExpected = [0, 1, 3, 6, 10, 15]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->clickGenerate($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasRoundRobin = true, $hasPreliminary = false);
                $this->checkAssertion($numArea, $numCompetitors, $numGroupsExpected);

            }
        }
    }

    /** @test */
    public function check_number_of_row_when_generating_direct_elimination_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 2, 2, 4, 4]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->clickGenerate($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasRoundRobin = false, $hasPreliminary = false);
                $this->checkAssertion($numArea, $numCompetitors, $numGroupsExpected);

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

    public function clickGenerate($numAreas, $numCompetitors, $preliminaryGroupSize, $hasRoundRobin, $hasPreliminary)
    {

        $this->visit('/kendo-tournaments')
            ->select($numAreas, 'fightingAreas')
            ->select($hasRoundRobin ? 0 : 1, 'treeType')
            ->select($preliminaryGroupSize, 'preliminaryGroupSize')
            ->select($numCompetitors, 'numFighters');


        if ($hasPreliminary) {
            $this->check('hasPreliminary');
        } else {
            $this->uncheck('hasPreliminary');
        }


        $this->press('save');
    }

    /**
     * @param $numArea
     * @param $numCompetitors
     * @param $numGroupsExpected
     */
    private function checkAssertion($numArea, $numCompetitors, $numGroupsExpected)
    {
        for ($area = 1; $area <= $numArea; $area++) {
            $count = FightersGroup::where('championship_id', $this->championship->id)
                ->where('area', $area)->count();

            if ((int)($numCompetitors / $numArea) <= 1) {
                $this->assertTrue($count == 0);
            } else {
                $expected = (int)($numGroupsExpected[$numCompetitors - 1] / $numArea);

                if ($count != $expected) {
                    dd(['NumCompetitors' => $numCompetitors],
                        ['NumArea' => $numArea],
                        ['Real' => $count],
                        ['Excepted' => $expected],
                        ['numGroupsExpected[' . ($numCompetitors - 1) . ']' => $numGroupsExpected[$numCompetitors - 1] . ' / ' . $numArea]);
                }
                $this->assertTrue($count == $expected);
            }
        }
    }
}
