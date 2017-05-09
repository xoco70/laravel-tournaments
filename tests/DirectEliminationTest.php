<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Tournament;

class DirectEliminationTest extends TestCase
{
//    use DatabaseTransactions;

    protected $root;
    protected $tournament, $championshipWithComp, $championshipWithTeam, $settings, $users;


    public function setUp()
    {
        parent::setUp();
        $this->tournament = $tournament = Tournament::with(
            'competitors',
            'championshipSettings'
        )->first();


        $this->championshipWithComp = Championship::with('teams', 'users', 'category', 'settings', 'fightersGroups.fights')->find($this->tournament->championships[0]->id);
        $this->championshipWithTeam = Championship::with('teams', 'users', 'category', 'settings', 'fightersGroups.fights')->find($this->tournament->championships[0]->id);
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

    /** @test */
    public function it_saves_fight_to_next_round_when_possible()
    {
        // Get the case when n^2-1 to have a lot of BYES on first round
        $this->championshipWithComp;
        $this->generateTreeWithUI($numArea = 1, $numCompetitors = 9, $preliminaryGroupSize = 3, $hasPlayOff = false, $hasPreliminary = 0);

        // if each round, if C1 != Null && C2== null, match(n+1) should be updated
        // if each round, if C1 == Null && C2== null, match(n+1) should be updated
        $maxRounds = $this->championshipWithComp->fightersGroups()->max('round');

        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
            $fightsByRound = $this->championshipWithComp->fightsByRound($numRound)->get();
            foreach ($fightsByRound as $fight) {
                $parentFight = $fight->group->parent;

                if ($fight->c1 == null || $fight->c2 != null) {
                    // Check that match(n+1) has correct value
                    assertTrue($parentFight->c1, $fight->c2);
                }
                if ($fight->c1 != null || $fight->c2 == null) {
                    // Check that match(n+1) has correct value
                    assertTrue($parentFight->c1, $fight->c1);
                }
                if ($fight->c1 == null || $fight->c2 == null) {
                    // Check that match(n+1) has correct value
                    assertTrue($parentFight->c1, null);
                }
            }

        }


    }


}
