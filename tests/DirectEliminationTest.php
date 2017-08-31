<?php

namespace Xoco70\LaravelTournaments\Tests;

use Xoco70\LaravelTournaments\Models\ChampionshipSettings;

class DirectEliminationTest extends TestCase
{
    /** @test */
    public function check_number_of_row_when_generating_direct_elimination_tree()
    {
        $fightersInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 2, 2, 4, 4]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($fightersInTree as $numFighters) {
                $setting = factory(ChampionshipSettings::class)->make([
                    'championship_id' => $this->getChampionship(0)->id,
                    'fightingAreas' => $numArea,
                    'treeType' => ChampionshipSettings::DIRECT_ELIMINATION,
                    'hasPreliminary' => 0,
                    'isTeam' => 0,
                    'numFighters' => $numFighters
                ]);
                $this->generateTreeWithUI($setting);
                parent::checkGroupsNumber($this->championshipWithComp, $setting, $numGroupsExpected, __METHOD__);
            }
        }
    }

    /** @test */
    public function check_number_of_fights_when_direct_elimination_tree()
    {
        $isTeams = [0, 1];
        $numFights = [
            1 => [1 => 0, 2 => 1, 3 => 2, 4 => 2, 5 => 4, 6 => 4, 7 => 4, 8 => 4,],
            2 => [1 => 0, 2 => 0, 3 => 0, 4 => 2, 5 => 4, 6 => 4, 7 => 4, 8 => 4,],
            4 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 4,],
        ];
        foreach ($isTeams as $isTeam) {
            $championship = $this->getChampionship($isTeam);
            foreach ($numFights as $numArea => $numFightPerArea) {
                foreach ($numFightPerArea as $numFighters => $numFightsExpected) {
                    $setting = factory(ChampionshipSettings::class)->make([
                        'championship_id' => $this->getChampionship($isTeam)->id,
                        'fightingAreas' => $numArea,
                        'treeType' => ChampionshipSettings::DIRECT_ELIMINATION,
                        'hasPreliminary' => 0,
                        'isTeam' => $isTeam,
                        'numFighters' => $numFighters
                    ]);
                    $this->generateTreeWithUI($setting);
                    parent::checkFightsNumber($championship, $setting, $numFightsExpected, __METHOD__);
                }
            }

        }
    }

    /** @test */
    public function it_saves_fight_to_next_round_when_possible()
    {
        $isTeam = 0;
        $setting = factory(ChampionshipSettings::class)->make([
            'championship_id' => $this->getChampionship($isTeam)->id,
            'fightingAreas' => 1,
            'treeType' => ChampionshipSettings::DIRECT_ELIMINATION,
            'hasPreliminary' => 0,
            'isTeam' => $isTeam,
            'numFighters' => 5
        ]);
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
        $toUpdate = 'c2';
        if ($key % 2 == 0) { // Even
            $toUpdate = 'c1';
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
