<?php

namespace Xoco70\KendoTournaments\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectEliminationTest extends TestCase
{
//    use DatabaseTransactions;

    /** @test */
    public function check_number_of_row_when_generating_direct_elimination_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 2, 2, 4, 4]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $setting = $this->createSetting($numArea, $numCompetitors, 0, 0, 3);
                $this->generateTreeWithUI($setting);
                parent::checkGroupsNumber($this->championshipWithComp, $numArea, $numCompetitors, $numGroupsExpected, __METHOD__);
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
                $setting = $this->createSetting($numArea, $numCompetitors, 0, 0, 3);// $team
                $this->generateTreeWithUI($setting);
                parent::checkFightsNumber($this->championshipWithComp, $numArea, $numCompetitors, $numFightsExpected, __METHOD__);

            }
        }
    }

    /** @test */
    public function it_saves_fight_to_next_round_when_possible()
    {
        $setting = $this->createSetting(1, 5, 0, 0, 3);
        $this->generateTreeWithUI($setting);

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
        $toUpdate = "c2";
        if ($key % 2 == 0) { // Even
            $toUpdate = "c1";
        }
        $parentFight = $fight->group->parent->fights->get(0);


        if (!$fight->c1) {
            $this->assertEquals($parentFight->$toUpdate, ($fight->c2 ?: null));
        }

        if (!$fight->c2) {
            $this->assertEquals($parentFight->$toUpdate, ($fight->c2 ?: null));
        }
    }


}
