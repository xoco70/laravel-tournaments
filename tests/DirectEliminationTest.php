<?php

namespace Xoco70\KendoTournaments\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Tournament;

class DirectEliminationTest extends TestCase
{
    //    use DatabaseTransactions;

    protected $root;
    protected $tournament, $championshipWithComp, $championshipWithTeam,
        $settings, $users;


    public function setUp()
    {
        parent::setUp();
        $this->tournament = Tournament::with(
            'competitors',
            'championshipSettings'
        )->first();


        $this->championshipWithComp = Championship::with(
            'teams', 'users', 'category', 'settings', 'fightersGroups.fights'
        )
            ->find($this->tournament->championships[0]->id);
        $this->championshipWithTeam = Championship::with(
            'teams', 'users', 'category', 'settings', 'fightersGroups.fights'
        )
            ->find($this->tournament->championships[0]->id);
    }


    /** @test */
    public function check_number_of_row_when_generating_direct_elimination_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 2, 2, 4, 4]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $this->generateTreeWithUI($numArea, $numCompetitors, 3, false, 0);
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
                $this->generateTreeWithUI($numArea, $numCompetitors, 3, false, 0);
                parent::checkFightsNumber($this->championshipWithComp, $numArea, $numCompetitors, $numFightsExpected, __METHOD__);
            }
        }
    }

    /** @test */
    public function it_saves_fight_to_next_round_when_possible()
    {
        $this->generateTreeWithUI(1, 5, 3, false, 0);


        // Get the case when n^2-1 to have a lot of BYES on first round

        // if each round, if C1 != Null && C2== null, match(n+1) should be updated
        // if each round, if C1 == Null && C2== null, match(n+1) should be updated
        $maxRounds = $this->championshipWithComp->fightersGroups()->max('round');

        for ($numRound = 1; $numRound < $maxRounds; $numRound++) {
            $fightsByRound = $this->championshipWithComp->fightsByRound(1)->get();
            foreach ($fightsByRound as $key => $fight) {
                $this->checkParentHasBeenFilled($key, $fight);
            }
        }
    }

    /**
     * @param $key
     * @param $fight
     */
    private function checkParentHasBeenFilled($key, $fight)
    {
        if ($key % 2 == 0) { // Even
            $toUpdate = "c1";
        } else { // Odd
            $toUpdate = "c2";
        }
        $parentFight = $fight->group->parent->fights->get(0);

        if ($fight->c1 == null) {
            if ($fight->c2 == null) {
                // C1 and C2 Is Bye
                $this->assertEquals($parentFight->$toUpdate, null);
            } else {
                // C1 Is Bye
                $this->assertEquals($parentFight->$toUpdate, $fight->c2);
            }
        } else {
            if ($fight->c2 == null) {
                // C2 Is Bye
                $this->assertEquals($parentFight->$toUpdate, $fight->c1);
            } else {
                // C1 and C2 Are all set
                $this->assertEquals($parentFight->$toUpdate, null);
            }
        }
    }
}
