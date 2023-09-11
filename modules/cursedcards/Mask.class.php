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
 * Mask.class.php
 * 
 * Mask card object
 */

class Mask extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Mask');
        $this->type = MASK;
        $this->cssClass = "dgit-card-mask-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
        $this->statName = 'masks';
    }

    /**
     * Build tooltip text for Mask
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect a Mask, immediately pass 1 Ghost per Mask card in your set to the player to your right.');
    }

    /**
     * Trigger effect of mask card
     * @param mixed $args
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];
        $maskCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), MASK);
        $playerOnRight = $this->game->playerManager->getPlayerOnRight($player);
        $ghostsToPass = min(count($maskCards), $player->getGhostTokens());

        $this->game->playerManager->adjustPlayerGhosts($player->getId(), $ghostsToPass * -1);
        $this->game->playerManager->adjustPlayerGhosts($playerOnRight->getId(), $ghostsToPass);

        $this->game->notifyAllPlayers(    
            TRIGGER_MASK,    
            clienttranslate('${player_name} passes ${ghostAmount} ${plural} to ${other_player_name} from collecting a Mask'),
            array(
                'i18n' => ['plural'],
                'player_name' => $this->game->getActivePlayerName(),
                'ghostAmount' => $ghostsToPass,
                'plural' => $ghostsToPass == 1 ? clienttranslate('Ghost') : clienttranslate('Ghosts'),
                'other_player_name' => $this->game->playerManager->getPlayerNameColorDiv($playerOnRight),
                'currentPlayer' => $player->getUiData(),
                'otherPlayer' => $playerOnRight->getUiData(),
            )
        );
    }
}