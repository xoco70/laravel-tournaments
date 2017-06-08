<?php

namespace Xoco70\KendoTournaments\TreeGen;

use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Team;

class CreateDirectEliminationTree
{
    public $firstRoundName;
    public $groupsByRound;
    public $hasPrelimimary;
    public $brackets = [];
    public $championship;
    public $numFighters;
    public $noRounds;
    public $playerWrapperHeight = 30;
    public $matchWrapperWidth = 150;
    public $roundSpacing = 40;
    public $matchSpacing = 42;
    public $borderWidth = 3;

    public function __construct($groupsByRound, $championship, $hasPreliminary)
    {
        $this->championship = $championship;
        $this->groupsByRound = $groupsByRound;
        $this->hasPreliminary = $hasPreliminary;

        $this->firstRoundName = $groupsByRound->first()->map(function ($item) use ($championship) {
            $fighters = $item->getFightersWithBye();
            $fighter1 = $fighters->get(0);
            $fighter2 = $fighters->get(1);
            return [$fighter1, $fighter2];
        })->flatten()->all();
    }

    public function build()
    {

        $fighters = $this->firstRoundName;
        $this->numFighters = count($fighters);


        //Calculate the size of the first full round - for example if you have 5 fighters, then the first full round will consist of 4 fighters
        $this->noRounds = log($this->numFighters, 2);
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
        $this->assignFightersToBracket($roundNumber, $this->hasPreliminary);
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

    /**
     * Print Round Titles
     */
    public function printRoundTitles()
    {

        if ($this->numFighters == 2) {

            $roundTitles = array('Final');

        } elseif ($this->numFighters <= 4) {

            $roundTitles = array('Semi-Finals', 'Final');

        } elseif ($this->numFighters <= 8) {

            $roundTitles = array('Quarter-Finals', 'Semi-Finals', 'Final');

        } else {

            $roundTitles = array('Quarter-Finals', 'Semi-Finals', 'Final');
            $noRounds = ceil(log($this->numFighters, 2));
            $noTeamsInFirstRound = pow(2, ceil(log($this->numFighters) / log(2)));
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

        foreach ($this->championship->fighters as $fighter) {
            $html = $this->addOptionToSelect($selected, $fighter, $html);
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
     * @param $numRound
     */
    private function assignFightersToBracket($numRound, $hasPreliminary)
    {
        //TODO When Preliminary, we get a problem : Round 2 to 2, or get rounNumber = 1, and fails
        for ($roundNumber = $numRound; $roundNumber <= $this->noRounds; $roundNumber++) {
            $groupsByRound = $this->groupsByRound->get($roundNumber + $hasPreliminary);
            for ($matchNumber = 1; $matchNumber <= ($this->numFighters / pow(2, $roundNumber)); $matchNumber++) {
                $fight = $groupsByRound[$matchNumber - 1]->fights[0];

                if ($this->championship->category->isTeam()) {
                    $fighter1 = $fight->team1;
                    $fighter2 = $fight->team2;

                } else {
                    $fighter1 = $fight->competitor1;
                    $fighter2 = $fight->competitor2;
                }
                $this->brackets[$roundNumber][$matchNumber] = [$fighter1, $fighter2];
            }
        }
    }

    /**
     * @param $selected
     * @param $fighter
     * @param $html
     * @return string
     */
    private function addOptionToSelect($selected, $fighter, $html): string
    {
        if ($fighter != null) {
            $select = $selected != null && $selected->id == $fighter->id ? ' selected' : '';
            $html .= '<option' . $select
                . ' value='
                . ($fighter->id ?? '')
                . '>'
                . $fighter->name
                . '</option>';

        }
        return $html;
    }
}
