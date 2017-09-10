<?php

namespace Xoco70\LaravelTournaments\Tests;

use Xoco70\LaravelTournaments\Models\ChampionshipSettings;

class SingleEliminationTest extends TestCase
{
    /** @test */
    public function check_number_of_row_when_generating_single_elimination_tree()
    {
        $fightersInTree = [1, 2, 3, 4, 5, 6]; // ,  7,  8,  9, 10, 11, 12, 13, 14
        $numGroupsExpected = [0, 1, 2, 2, 4, 4]; // , 21, 28, 36, 45, 55, 66, 78, 91
        $numAreas = [1];
        foreach ($numAreas as $numArea) {
            foreach ($fightersInTree as $numFighters) {
                $setting = factory(ChampionshipSettings::class)->make([
                    'championship_id' => $this->getChampionship(0)->id,
                    'fightingAreas'   => $numArea,
                    'treeType'        => ChampionshipSettings::SINGLE_ELIMINATION,
                    'hasPreliminary'  => 0,
                    'isTeam'          => 0,
                    'numFighters'     => $numFighters,
                ]);
                $this->generateTreeWithUI($setting);
                parent::checkGroupsNumber($setting, $numGroupsExpected, __METHOD__);
            }
        }
    }

    /** @test */
    public function check_number_of_fights_when_single_elimination_tree()
    {
        $isTeams = [0, 1];
        $numFights = [
            1 => [1 => 0, 2 => 1, 3 => 2, 4 => 2, 5 => 4, 6 => 4, 7 => 4, 8 => 4],
            2 => [1 => 0, 2 => 0, 3 => 0, 4 => 2, 5 => 4, 6 => 4, 7 => 4, 8 => 4],
            4 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 4],
        ];
        foreach ($isTeams as $isTeam) {
            $championship = $this->getChampionship($isTeam);
            foreach ($numFights as $numArea => $numFightPerArea) {
                foreach ($numFightPerArea as $numFighters => $numFightsExpected) {
                    $setting = factory(ChampionshipSettings::class)->make([
                        'championship_id' => $this->getChampionship($isTeam)->id,
                        'fightingAreas'   => $numArea,
                        'treeType'        => ChampionshipSettings::SINGLE_ELIMINATION,
                        'hasPreliminary'  => 0,
                        'isTeam'          => $isTeam,
                        'numFighters'     => $numFighters,
                    ]);
                    $this->generateTreeWithUI($setting);
                    parent::checkFightsNumber($setting, $numFightsExpected, __METHOD__);
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
            'fightingAreas'   => 1,
            'treeType'        => ChampionshipSettings::SINGLE_ELIMINATION,
            'hasPreliminary'  => 0,
            'isTeam'          => $isTeam,
            'numFighters'     => 5,
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

    /** @test */
    public function you_can_update_manually_single_elimination_tree_fighters()
    {
        $setting = factory(ChampionshipSettings::class)->make([
            'championship_id' => $this->getChampionship(0)->id,
            'fightingAreas'   => 1,
            'treeType'        => ChampionshipSettings::SINGLE_ELIMINATION,
            'hasPreliminary'  => 0,
            'isTeam'          => 0,
            'numFighters'     => 5,
        ]);
        $this->generateTreeWithUI($setting);
        $competitors = $this->championshipWithComp->competitors; // 5 comp

        $this->select([
            $competitors->get(0)->id,
            $competitors->get(1)->id,
            $competitors->get(2)->id,
            $competitors->get(3)->id,
            $competitors->get(4)->id,
        ], 'singleElimination_fighters[]')
            ->press('update');

        $fights = $this->championshipWithComp->fights;
        $this->assertEquals($competitors->get(0)->id, $fights->get(0)->c1);
        $this->assertEquals($competitors->get(1)->id, $fights->get(0)->c2);
        $this->assertEquals($competitors->get(2)->id, $fights->get(1)->c1);
        $this->assertEquals($competitors->get(3)->id, $fights->get(1)->c2);
        $this->assertEquals($competitors->get(4)->id, $fights->get(2)->c1);
    }

    /** @test */
    public function you_can_update_manually_single_elimination_tree_winner_id()
    {
        $setting = factory(ChampionshipSettings::class)->make([
            'championship_id' => $this->getChampionship(0)->id,
            'fightingAreas'   => 1,
            'treeType'        => ChampionshipSettings::SINGLE_ELIMINATION,
            'hasPreliminary'  => 0,
            'isTeam'          => 0,
            'numFighters'     => 5,
        ]);
        $this->generateTreeWithUI($setting);
        $fight = $this->championshipWithComp->fights->get(0);
        $this->assertNull($fight->winner_id);

        $this->select(['X', 'X'], 'score[]')
            ->press('update');
        if ($fight->c1 != null && $fight->c2 != null) {
            $this->assertNotNull($fight->winner_id);
        } else {
            $this->assertNull($fight->winner_id);
        }
    }

    /** @test */
    public function it_can_generate_single_elim_tree_with_16_fighters()
    {
        // This test is a regression test, used to fail
        $setting = factory(ChampionshipSettings::class)->make([
            'championship_id' => $this->getChampionship(0)->id,
            'fightingAreas'   => 1,
            'treeType'        => ChampionshipSettings::SINGLE_ELIMINATION,
            'hasPreliminary'  => 1,
            'isTeam'          => 0,
            'numFighters'     => 16,
        ]);
        $this->generateTreeWithUI($setting)
            ->assertResponseOk();
    }
}
