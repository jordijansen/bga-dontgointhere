<?php

/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Clock.class.php
 * 
 * Clock card object
 */

class Clock extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Clock');
        $this->type = CLOCK;
        $this->cssClass = "dgit-card-clock-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
        $this->statName = 'clocks';
    }

    /**
     * Build tooltip text for Clock
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('If you are the 1st player to collect a set of Clock cards whose Curse values add up to 8 or more, immediately dispel 2 Clock cards. After that, collecting a set of Clocks has no effect.');
    }

    /**
     * Trigger the effect from taking a Clock
     * @param mixed $args
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];
        // Clocks can only be triggered once, need to check if it's already happened or not
        $isClockCollected = $this->game->getGameStateValue(CLOCKS_COLLECTED);

        if($isClockCollected == DGIT_FALSE) {
            $clockCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), CLOCK);
            
            $totalCurseValue = 0;
            foreach($clockCards as $clockCard) {
                $totalCurseValue += $clockCard->getCurses();
            }

            // If Clocks haven't been triggered yet and player has a total of 8 or more curse, dispel 2 Clock cards with highest curse calue
            if($totalCurseValue >= 8) {
                $sortedClocks = $this->game->cardManager->sortCardsByCurseValueDesc($clockCards);
                $clocksToDispel = [];
                $clocksToDispel[] = $sortedClocks[0];
                $clocksToDispel[] = $sortedClocks[1];
                $curseValueDispeled = $sortedClocks[0]->getCurses() + $sortedClocks[1]->getCurses();

                $this->game->playerManager->adjustPlayerDispeled($player->getId(), 2);
                $this->game->playerManager->adjustPlayerCurses($player->getId(), $curseValueDispeled * -1);
                $this->game->incStat($totalCurseValue * -1, 'clocks_curses', $player->getId());
                $this->game->incStat(count($clocksToDispel), 'clocks_dispeled', $player->getId());
                $this->game->cardManager->moveCards($clocksToDispel, DISPELED);
                $this->game->setGameStateValue(CLOCKS_COLLECTED, DGIT_TRUE);

                $this->game->notifyAllPlayers(
                    DISPEL_CARDS,    
                    clienttranslate('${player_name} dispels ${amount} Clock cards worth a total of ${curses} Curses'),
                    array(
                        'player_name' => $this->game->getActivePlayerName(),
                        'amount' => 2,
                        'curses' => $curseValueDispeled,
                        'curseTotal' => $curseValueDispeled * -1,
                        'player' => $player->getUiData(),
                        'cards' => $this->game->cardManager->getUiDataFromCards($clocksToDispel),
                    )
                );
            }
        }
    }

}