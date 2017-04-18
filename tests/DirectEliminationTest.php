<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Tournament;

class DirectEliminationTest extends TestCase
{
    use DatabaseTransactions;

    protected $root;
    protected $tournament, $championshipWithComp,$championshipWithTeam, $settings, $users;


    public function setUp()
    {
        parent::setUp();
        $this->tournament = $tournament = Tournament::with(
            'competitors',
            'championshipSettings'
        )->first();



        $this->championshipWithComp = Championship::with('teams', 'users', 'category', 'settings')->find($this->tournament->championships[0]->id);
        $this->championshipWithTeam = Championship::with('teams', 'users', 'category', 'settings')->find($this->tournament->championships[0]->id);
    }


    /** @test */
    public function check_number_of_row_when_generating_direct_elimination_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 2, 2, 4, 4]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->generateTreeWithUI($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasPlayOff = false, $hasPreliminary = 0);
                parent::checkGroupsNumber($this->championshipWithComp, $numArea, $numCompetitors, $numGroupsExpected, __METHOD__);
                parent::checkGroupsNumber($this->championshipWithTeam, $numArea, $numCompetitors, $numGroupsExpected, __METHOD__);

            }
        }
    }

    /** @test */
    public function check_number_of_fights_when_direct_elimination_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8];
        $numFightsExpected = [0, 1, 2, 2, 4, 4, 4, 4];
        $numAreas = [1, 2];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->generateTreeWithUI($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasPlayOff = false, $hasPreliminary = 0);
                parent::checkFightsNumber($this->championshipWithComp, $numArea, $numCompetitors, $numFightsExpected, __METHOD__);
            }
        }
    }


}
