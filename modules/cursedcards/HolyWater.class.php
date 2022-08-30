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
 * HolyWater.class.php
 * 
 * Holy Water card object
 */

class HolyWater extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Holy Water');
        $this->type = HOLY_WATER;
        $this->cssClass = "dgit-card-holy-water-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Holy Water
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect 2 Holy Water cards, immediately discard half of your Ghost tokens, rounded down.');
    }

    /**
     * Trigger effect of taking a holy water card
     * @param mixed $args
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];
        $holyWaterCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), HOLY_WATER);

        // echo(count($holyWaterCards) % 2);
        // die('ok');
        
        if(count($holyWaterCards) % 2 == 0) {
            $currentGhosts = $player->getGhostTokens();
            $ghostsToDiscard = floor($currentGhosts / 2) * -1;
            
            if($ghostsToDiscard != 0) {
                $this->game->playerManager->adjustPlayerGhosts($player->getId(), $ghostsToDiscard);
                $this->game->notifyAllPlayers(
                    ADJUST_GHOSTS,    
                    clienttranslate('${player_name} collects a set of 2 Holy Water cards and discards ${number} ghosts'),
                    array(
                        'player_name' => $this->game->getActivePlayerName(),
                        'number' => $ghostsToDiscard * -1,
                        'playerId' => $player->getId(),
                        'amount' => $ghostsToDiscard,
                    )
                );
            }
        }
    }
}