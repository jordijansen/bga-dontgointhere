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
 * Twin.class.php
 * 
 * Twin card object
 */

class Twin extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Twin');
        $this->type = TWIN;
        $this->cssClass = "dgit-card-twin-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
        $this->statName = 'twins';
    }

    /**
     * Build tooltip text for Twin
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect 2 Twin cards with the same Curse value, immediately dispel those 2 Twin cards.');
    }

    /**
     * Trigger twin effect
     * @param mixed $args
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];
        $selectedCard = $args['selectedCard'];
        $twinCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), TWIN);
        $twins = [];
        $twins[] = $selectedCard;
        $matchingTwin = $this->game->cardManager->listContainsCard($twinCards, $selectedCard->getCurses(), $selectedCard->getId());

        if($matchingTwin != false) {
            $twins[] = $matchingTwin;
            $this->game->playerManager->adjustPlayerDispeled($player->getId(), 2);
            $this->game->playerManager->adjustPlayerCurses($player->getId(), $selectedCard->getCurses() * -2);
            $this->game->incStat($selectedCard->getCurses() * -2, 'twins_curses', $player->getId());
            $this->game->incStat(2, 'twins_dispeled', $player->getId());
            $this->game->cardManager->moveCards($twins, DISPELED);
            $this->game->notifyAllPlayers(
                DISPEL_CARDS,    
                clienttranslate('${player_name} dispels ${amount} Twin cards worth a total of ${curses} Curses'),
                array(
                    'player_name' => $this->game->getActivePlayerName(),
                    'amount' => 2,
                    'curses' => $selectedCard->getCurses() * 2,
                    'curseTotal' => $selectedCard->getCurses() * -2,
                    'player' => $player->getUiData(),
                    'cards' => $this->game->cardManager->getUiDataFromCards($twins),
                )
            );
        }
    }
}