<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Tournament;

class PreliminaryTest extends TestCase
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
    public function check_number_of_groups_when_generating_preliminary_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8];
        $numGroupsExpected = [0, 1, 1, 2, 2, 2, 4, 4];
        $numAreas = [1, 2, 4];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->generateTreeWithUI($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasPlayOff = false, $hasPreliminary = 1);
                parent::checkGroupsNumber($this->championship, $numArea, $numCompetitors, $numGroupsExpected, __METHOD__);
            }
        }
    }


//    /** @test */
//    public function check_number_of_fights_when_preliminary_tree()
//    {
//        $competitorsInTree = [ 1, 2, 3, 4, 5, 6, 7, 8];
////        $numFightsExpected = [ 0, 1, 2, 2, 4, 4, 4, 4];
//        $numAreas = [1, 2];
//        foreach ($numAreas as $numArea) {
//            foreach ($competitorsInTree as $numCompetitors) {
//                $this->generateTreeWithUI($numArea, $numCompetitors, $preliminaryGroupSize = 3, $hasPlayOff = false, $hasPreliminary = 1);
////                parent::checkFightsNumber($this->championship, $numArea, $numCompetitors, $numFightsExpected, __METHOD__);
//            }
//        }
//    }

}
