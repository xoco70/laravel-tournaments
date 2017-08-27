<?php

namespace Xoco70\LaravelTournaments\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectEliminationWPrelimTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function check_number_of_groups_when_generating_preliminary_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8];
        $numPreliminaryGroups = [
            1 => [
                $preliminaryGroupSize = 3 => [0, 0, 1, 2, 2, 2, 4, 4, 4, 8, 8],
                $preliminaryGroupSize = 4 => [0, 0, 0, 1, 2, 2, 2, 2, 4, 4, 4],
                $preliminaryGroupSize = 5 => [0, 0, 0, 0, 1, 2, 2, 2, 2, 2, 4],
            ],
            2 => [
                $preliminaryGroupSize = 3 => [0, 0, 0, 0, 0, 2, 4, 4, 4, 8, 8],
                $preliminaryGroupSize = 4 => [0, 0, 0, 0, 0, 0, 0, 2, 4, 4, 4],
                $preliminaryGroupSize = 5 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            ],
            4 => [
                $preliminaryGroupSize = 3 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                $preliminaryGroupSize = 4 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                $preliminaryGroupSize = 5 => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            ],
        ];
        foreach ($numPreliminaryGroups as $numArea => $prelimGroupsByArea) {
            foreach ($prelimGroupsByArea as $preliminaryGroupSize => $numPreliminaryGroup) {
                foreach ($competitorsInTree as $numCompetitors) {
//                    $isTeam = rand(0,1);
                    $setting = $this->createSetting($numArea, $numCompetitors, 0, 1, $preliminaryGroupSize); // $team
                    $this->generateTreeWithUI($setting);
                    parent::checkGroupsNumber($this->championshipWithComp->fresh(), $setting, $numPreliminaryGroup, __METHOD__);
                }
            }
        }
    }

    /** @test */
    public function check_number_of_fights_when_preliminary_tree()
    {
        $isTeams = [0, 1];
        $numFights = [
            1 => [ // numArea
                1 => 0,
                2 => 0,
                3 => 3,
                4 => 6,
                5 => 6,
                6 => 6,
                7 => 12,
                8 => 12,
            ],
            2 => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 6,
                7 => 12,
                8 => 12,
            ],
            4 => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
            ],
        ];
        foreach ($isTeams as $isTeam) {
            $isTeam
                ? $championship = $this->championshipWithTeam
                : $championship = $this->championshipWithComp;
            foreach ($numFights as $numArea => $numFightPerArea) {
                foreach ($numFightPerArea as $numCompetitors => $numFightsExpected) {
                    $setting = $this->createSetting($numArea, $numCompetitors, $isTeam, 1, 3); // $team
                    $this->generateTreeWithUI($setting);
                    parent::checkFightsNumber($championship, $setting, $numFightsExpected, __METHOD__);
                }
            }
        }
    }
}
