<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Testing\DatabaseTransactions;

class PreliminaryTest extends TestCase
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
            ]
        ];
        foreach ($numPreliminaryGroups as $numArea => $prelimGroupsByArea) {
            foreach ($prelimGroupsByArea as $preliminaryGroupSize => $numPreliminaryGroup) {
                foreach ($competitorsInTree as $numCompetitors) {
                    $setting = $this->createSetting($numArea, $numCompetitors, 0, 1, $preliminaryGroupSize);// $team
                    $this->generateTreeWithUI($setting);
                    parent::checkGroupsNumber($this->championshipWithComp->fresh(), $numArea, $numCompetitors, $numPreliminaryGroup, __METHOD__);
                }
            }
        }
    }


    /** @test */
    public function check_number_of_fights_when_preliminary_tree()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8];
        $numFightsExpected = [0, 1, 2, 2, 4, 4, 4, 4];

        $numAreas = [1, 2, 4];
        $numPreliminaryGroups = [3];

        foreach ($numPreliminaryGroups as $numPreliminaryGroup) {
            foreach ($numAreas as $numArea) {
                foreach ($competitorsInTree as $numCompetitors) {
                    $setting = $this->createSetting($numArea, $numCompetitors, 0, 1, $numPreliminaryGroup);// $team
                    $this->generateTreeWithUI($setting);
                    parent::checkFightsNumber($this->championshipWithComp, $numArea, $numCompetitors, $numFightsExpected, __METHOD__);
                }
            }
        }
    }

}
