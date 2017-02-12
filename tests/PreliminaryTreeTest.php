<?php
namespace Xoco70\KendoTournaments\Tests;

use BrowserKitTestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Tournament;
use App\User;

class PreliminaryTreeTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    protected $root, $tournament, $championship;


    public function setUp()
    {
        parent::setUp();
        $this->root = factory(User::class)->create(['role_id' => Config::get('constants.ROLE_SUPERADMIN')]);
        $this->logWithUser($this->root);

    }

    /** @test */
    public function check_number_of_row_when_generating_tournament()
    {
        $competitorsInTree = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
        $numGroupsExpected = [0, 1, 1, 2, 2, 2, 4, 4, 4, 4, 4, 4, 8, 8, 8, 8, 8, 8];
        $numAreas = [1, 2, 4];
//        $preliminarySizeGroup = 3;
        foreach ($numAreas as $numArea) {
            foreach ($competitorsInTree as $numCompetitors) {
                $tournament = factory(Tournament::class)->create();

                $championship = factory(Championship::class)->create([
                    'tournament_id' => $tournament->id,
                    'category_id' => 1,
                ]);

                factory(ChampionshipSettings::class)->create([
                    'championship_id' => $championship->id,
                    'hasPreliminary' => 1,
                    'teamSize' => null,
                    'fightingAreas' => $numArea,
                    'preliminaryWinner' => 1,
                    'preliminaryGroupSize' => 3,
                ]);


                $users = factory(User::class, $numCompetitors)->create(['role_id' => Config::get('constants.ROLE_USER')]);
                if ($users instanceof User) {
                    $user = $users;
                    $users = new Collection();
                    $users->push($user);
                }
                $this->makeCompetitors($championship, $users);
                $this->generatePreliminaryTree($tournament);
                for ($area = 1; $area <= $numArea; $area++) {
                    $count = Tree::where('championship_id', $championship->id)
                        ->where('area', $area)->count();


                    if ((int )($numCompetitors / $numArea) <= 1) {
                        $this->assertTrue($count == 0);
                    } else {
                        $expected = (int)($numGroupsExpected[$numCompetitors - 1] / $numArea);

                        if ($count != $expected) {
                            dd(["Type" => "RoundRobin"],
                                ["NumCompetitors" => $numCompetitors],
                                ["NumArea" => $numArea],
                                ["Real" => $count],
                                ["Excepted" => $expected],
                                ["numGroupsExpected[" . ($numCompetitors - 1) . "]" => $numGroupsExpected[$numCompetitors - 1] . " / " . $numArea]);
                        }
                        $this->assertTrue($count == $expected);
                    }
                }
            }
        }

    }


    /**
     * @param $users
     */
    public function makeCompetitors($championship, $users)
    {
        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'user_id' => $user->id,
                'championship_id' => $championship->id,
                'confirmed' => 1]);
        }
    }

    public function generatePreliminaryTree(Tournament $tournament)
    {
        $this->visit('/tournaments/' . $tournament->slug . "/edit")
            ->click('#competitors')
            ->press('generate');
    }
}
