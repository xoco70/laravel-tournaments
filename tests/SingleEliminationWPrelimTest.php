<?php

namespace Xoco70\LaravelTournaments\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;

class SingleEliminationWPrelimTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function check_number_of_groups_when_generating_preliminary_tree()
    {
        $fightersInTree = [1, 2, 3, 4, 5, 6, 7, 8];
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
                foreach ($fightersInTree as $numFighters) {
                    $setting = factory(ChampionshipSettings::class)->make([
                        'championship_id'      => $this->championshipWithComp->fresh()->id,
                        'fightingAreas'        => $numArea,
                        'numFighters'          => $numFighters,
                        'isTeam'               => 0,
                        'treeType'             => ChampionshipSettings::SINGLE_ELIMINATION,
                        'hasPreliminary'       => 1,
                        'preliminaryGroupSize' => $preliminaryGroupSize,
                    ]);
                    $this->generateTreeWithUI($setting);
                    parent::checkGroupsNumber($setting, $numPreliminaryGroup, __METHOD__);
                }
            }
        }
    }

    /** @test */
    public function check_number_of_fights_when_preliminary_tree()
    {
        $isTeams = [0, 1];

        $numFights = [
            $groupsSize = 3 => [
                $numArea = 1 => [1 => 0, 2 => 0, 3 => 3, 4 => 6, 5 => 6, 6 => 6, 7 => 12, 8 => 12],
                $numArea = 2 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 6, 7 => 12, 8 => 12],
//                $numArea = 4 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0,],
            ],
            $groupsSize = 4 => [
                $numArea = 1 => [1 => 0, 2 => 0, 3 => 0, 4 => 6, 5 => 12, 6 => 12, 7 => 12, 8 => 12],
                $numArea = 2 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 12],
//                $numArea = 4 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0,],
            ],
            $groupsSize = 5 => [
                $numArea = 1 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 15, 6 => 30, 7 => 30, 8 => 30], // Some fights are removed from the list
//                $numArea = 2 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0,],
//                $numArea = 4 => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0,],
            ],
        ];

        foreach ($isTeams as $isTeam) {
            $isTeam
                ? $championship = $this->championshipWithTeam
                : $championship = $this->championshipWithComp;

            foreach ($numFights as $numGroup => $numFightGroupSize) {
                foreach ($numFightGroupSize as $numArea => $numFightPerArea) {
                    foreach ($numFightPerArea as $numFighters => $numFightsExpected) {
                        $setting = factory(ChampionshipSettings::class)->make([
                            'championship_id'      => $championship->id,
                            'fightingAreas'        => $numArea,
                            'numFighters'          => $numFighters,
                            'isTeam'               => $isTeam,
                            'treeType'             => ChampionshipSettings::SINGLE_ELIMINATION,
                            'hasPreliminary'       => 1,
                            'preliminaryGroupSize' => $numGroup,
                        ]);

                        $this->generateTreeWithUI($setting);
                        parent::checkFightsNumber($setting, $numFightsExpected, __METHOD__);
                    }
                }
            }
        }
    }

    /** @test */
    public function it_can_generate_prelim_tree_with_12_fighters()
    {
        $setting = factory(ChampionshipSettings::class)->make([
            'championship_id'      => $this->getChampionship(0)->id,
            'fightingAreas'        => 1,
            'treeType'             => ChampionshipSettings::SINGLE_ELIMINATION,
            'hasPreliminary'       => 1,
            'isTeam'               => 0,
            'numFighters'          => 12,
            'preliminaryGroupSize' => 3,
        ]);
        $this->generateTreeWithUI($setting);

        // Check fights orders
        $championship = Championship::find(1);

        foreach ($championship->fightersGroups as $group) {
            $competitorsGroup = $group->competitors;
            $fights = $group->fights;
            $this->assertEquals(optional($fights->get(0))->c1, optional($competitorsGroup->get(0))->id);
            $this->assertEquals(optional($fights->get(0))->c2, optional($competitorsGroup->get(1))->id);

            $this->assertEquals(optional($fights->get(1))->c1, optional($competitorsGroup->get(1))->id);
            $this->assertEquals(optional($fights->get(1))->c2, optional($competitorsGroup->get(2))->id);

            $this->assertEquals(optional($fights->get(2))->c1, optional($competitorsGroup->get(2))->id);
            $this->assertEquals(optional($fights->get(2))->c2, optional($competitorsGroup->get(0))->id);
        }
    }
}
