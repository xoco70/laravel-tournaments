<?php

namespace Xoco70\LaravelTournaments\Tests;

use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\FightersGroup;

class PlayOffTest extends TestCase
{
    /** @test */
    public function check_number_of_row_when_generating_playoff_tree()
    {
        $setting = factory(ChampionshipSettings::class)->make([
            'championship_id' => $this->getChampionship(0)->id,
            'fightingAreas' => 1,
            'treeType' => ChampionshipSettings::PLAY_OFF,
            'hasPreliminary' => 0,
            'isTeam' => 0,
            'numFighters' => rand(3, 10)
        ]);
        $this->generateTreeWithUI($setting);
        $count = FightersGroup::where('championship_id', $setting->championship->id)
            ->where('round', 1)
            ->count();
        $this->assertTrue($count == 1);
    }

    /** @test */
    public function check_number_of_fights_when_generating_playoff_tree()
    {
        $fightersInTree = [1, 2, 3, 4, 5, 6, 7, 8];
        // When none, we add a dummy player that will never display
        $numFightsExpected = [1 => 0, 2 => 1, 3 => 6, 4 => 6, 5 => 15, 6 => 15, 7 => 28, 8 => 28, ];
        foreach ($fightersInTree as $numFighters) {
            $setting = factory(ChampionshipSettings::class)->make([
                'championship_id' => $this->getChampionship(0)->id,
                'fightingAreas' => 1,
                'treeType' => ChampionshipSettings::PLAY_OFF,
                'hasPreliminary' => 0,
                'isTeam' => 0,
                'numFighters' => $numFighters
            ]);
            $this->generateTreeWithUI($setting);
            parent::checkFightsNumber($setting, $numFightsExpected[$numFighters], __METHOD__);
        }
    }
}
