<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Team;

class CreateDirectEliminationTree
{
    public $firstRoundName;
    public $names;
    public $brackets = [];
    public $championship;
    public $noTeams;
    public $noRounds;
    public $playerWrapperHeight = 30;
    public $matchWrapperWidth = 150;
    public $roundSpacing = 40;
    public $matchSpacing = 42;
    public $borderWidth = 3;

    public function __construct($names, $championship)
    {
        $this->championship = $championship;
        $this->names = $names;

        $this->firstRoundName = $names->first()->map(function($item) use ($championship) {
            $fighters = $item->getFighters();
            $fighter1 = $fighters->get(0);
            $fighter2 = $fighters->get(1);

            return [$fighter1, $fighter2];
        })->flatten()->all();

        $this->run();

    }

    public function run()
    {

        $fighters = $this->firstRoundName;
        $this->noTeams = count($fighters);


        //Calculate the size of the first full round - for example if you have 5 fighters, then the first full round will consist of 4 fighters
        $this->noRounds = log($this->noTeams, 2);

        $roundNumber = 1;

        //Group 2 fighters into a match
        $matches = array_chunk($fighters, 2);

        //If there's already a match in the match array, then that means the next round is round 2, so increase the round number
        if (count($this->brackets)) {
            $roundNumber++;
        }

        $countMatches = count($matches);
        //Create the first full round of fighters, some may be blank if waiting on the results of a previous round
        for ($i = 0; $i < $countMatches; $i++) {
            $this->brackets[$roundNumber][$i + 1] = $matches[$i];
        }

        //Create the result of the empty rows for this tournament
        $this->assignFightersToBracket($roundNumber);
        $this->assignPositions();
    }

    private function assignPositions()
    {

        //Variables required for figuring outing the height of the vertical connectors

        $spaceFactor = 0.5;
        $playerHeightFactor = 1;

        foreach ($this->brackets as $roundNumber => &$round) {

            foreach ($round as $matchNumber => &$match) {

                //Give teams a nicer index

                $match['playerA'] = $match[0];
                $match['playerB'] = $match[1];

                unset($match[0]);
                unset($match[1]);

                //Figure out the bracket positions

                $match['matchWrapperTop'] = (((2 * $matchNumber) - 1) * (pow(2, ($roundNumber) - 1)) - 1) * (($this->matchSpacing / 2) + $this->playerWrapperHeight);
                $match['matchWrapperLeft'] = ($roundNumber - 1) * ($this->matchWrapperWidth + $this->roundSpacing - 1);
                $match['vConnectorLeft'] = floor($match['matchWrapperLeft'] + $this->matchWrapperWidth + ($this->roundSpacing / 2) - ($this->borderWidth / 2));
                $match['vConnectorHeight'] = ($spaceFactor * $this->matchSpacing) + ($playerHeightFactor * $this->playerWrapperHeight) + $this->borderWidth;
                $match['vConnectorTop'] = $match['hConnectorTop'] = $match['matchWrapperTop'] + $this->playerWrapperHeight;
                $match['hConnectorLeft'] = ($match['vConnectorLeft'] - ($this->roundSpacing / 2)) + 2;
                $match['hConnector2Left'] = $match['matchWrapperLeft'] + $this->matchWrapperWidth + ($this->roundSpacing / 2);

                //Adjust the positions depending on the match number

                if (!($matchNumber % 2)) {
                    $match['hConnector2Top'] = $match['vConnectorTop'] -= ($match['vConnectorHeight'] - $this->borderWidth);
                } else {
                    $match['hConnector2Top'] = $match['vConnectorTop'] + ($match['vConnectorHeight'] - $this->borderWidth);
                }

            }

            //Update the spacing variables

            $spaceFactor *= 2;
            $playerHeightFactor *= 2;

        }

    }

    public function printBrackets()
    {

        $this->printRoundTitles();

        echo '<div id="brackets-wrapper">';

        foreach ($this->brackets as $roundNumber => $round) {

            foreach ($round as $match) {

                echo '<div class="match-wrapper" style="top: ' . $match['matchWrapperTop'] . 'px; left: ' . $match['matchWrapperLeft'] . 'px; width: ' . $this->matchWrapperWidth . 'px;">
                        <input type="text" class="score">'
                    . $this->getPlayerList($match['playerA']) .
                    '<div class="match-divider">
                        </div>
                        <input type="text" class="score">'
                    . $this->getPlayerList($match['playerB']) .
                    '</div>';

                if ($roundNumber != $this->noRounds) {

                    echo '<div class="vertical-connector" style="top: ' . $match['vConnectorTop'] . 'px; left: ' . $match['vConnectorLeft'] . 'px; height: ' . $match['vConnectorHeight'] . 'px;"></div>
                          <div class="horizontal-connector" style="top: ' . $match['hConnectorTop'] . 'px; left: ' . $match['hConnectorLeft'] . 'px;"></div>
                          <div class="horizontal-connector" style="top: ' . $match['hConnector2Top'] . 'px; left: ' . $match['hConnector2Left'] . 'px;"></div>';

                }

            }

        }


        echo '</div>';
    }

    /**
     * Print Round Titles
     */
    public function printRoundTitles()
    {

        if ($this->noTeams == 2) {

            $roundTitles = array('Final');

        } elseif ($this->noTeams <= 4) {

            $roundTitles = array('Semi-Finals', 'Final');

        } elseif ($this->noTeams <= 8) {

            $roundTitles = array('Quarter-Finals', 'Semi-Finals', 'Final');

        } else {

            $roundTitles = array('Quarter-Finals', 'Semi-Finals', 'Final');
            $noRounds = ceil(log($this->noTeams, 2));
            $noTeamsInFirstRound = pow(2, ceil(log($this->noTeams) / log(2)));
            $tempRounds = array();

            //The minus 3 is to ignore the final, semi final and quarter final rounds

            for ($i = 0; $i < $noRounds - 3; $i++) {
                $tempRounds[] = 'Last ' . $noTeamsInFirstRound;
                $noTeamsInFirstRound /= 2;
            }

            $roundTitles = array_merge($tempRounds, $roundTitles);

        }

        echo '<div id="round-titles-wrapper">';

        foreach ($roundTitles as $key => $roundTitle) {

            $left = $key * ($this->matchWrapperWidth + $this->roundSpacing - 1);

            echo '<div class="round-title" style="left: ' . $left . 'px;">' . $roundTitle . '</div>';

        }

        echo '</div>';

    }

    /**
     * @param $selected
     * @return string
     */
    public function getPlayerList($selected)
    {

        $html = '<select>
                <option' . ($selected == '' ? ' selected' : '') . '></option>';

        foreach ($this->championship->competitors as $competitor) {
            $html = $this->addOptionToSelect($selected, $competitor, $html);
        }

        $html .= '</select>';

        return $html;

    }

    public function getNewFighter()
    {
        if ($this->championship->category->isTeam()) {
            return new Team;
        }
        return new Competitor;
    }

    /**
     * @param $roundNumber
     */
    private function assignFightersToBracket($roundNumber)
    {
        for ($roundNumber += 1; $roundNumber <= $this->noRounds; $roundNumber++) {
            for ($matchNumber = 1; $matchNumber <= ($this->noTeams / pow(2, $roundNumber)); $matchNumber++) {
                if ($this->championship->category->isTeam()) {
                    $fighter1 = $this->names->get($roundNumber)[0]->fights[$matchNumber - 1]->team1;
                    $fighter2 = $this->names->get($roundNumber)[0]->fights[$matchNumber - 1]->team2;
                } else {
                    $fighter1 = $this->names->get($roundNumber)[$matchNumber - 1]->fights[0]->competitor1;
                    $fighter2 = $this->names->get($roundNumber)[$matchNumber - 1]->fights[0]->competitor2;
                }
                $this->brackets[$roundNumber][$matchNumber] = [$fighter1, $fighter2];
            }
        }
    }

    /**
     * @param $selected
     * @param $competitor
     * @param $html
     * @return string
     */
    private function addOptionToSelect($selected, $competitor, $html): string
    {
        if ($competitor != null) {
            $select = $selected != null && $selected->id == $competitor->id ? ' selected' : '';
            $html .= '<option' . $select
                . ' value='
                . ($competitor->id ?? '')
                . '>'
                . $competitor->name
                . '</option>';

        }
        return $html;
    }
}
