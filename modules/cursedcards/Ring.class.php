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
 * Ring.class.php
 * 
 * Ring card object
 */
class Ring extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Ring');
        $this->type = RING;
        $this->cssClass = "dgit-card-ring-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Ring
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect 4 Ring cards, immediately dispel those 4 Ring cards.');
    }

    /**
     * Trigger ring effect
     * @param mixed $args
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];

        $ringCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), RING);
        if(count($ringCards) == 4) {
            $this->game->playerManager->adjustPlayerDispeled($player->getId(), 4);
            $curseTotal = 0;
            foreach($ringCards as $ringCard) {
                $curseTotal += $ringCard->getCurses();
            }
            $this->game->playerManager->adjustPlayerCurses($player->getId(), $curseTotal * -1);
            $this->game->cardManager->moveCards($ringCards, DISPELED);

            $this->game->notifyAllPlayers(
                DISPEL_CARDS,    
                clienttranslate('${player_name} dispels ${amount} Ring cards worth a total of ${curses} Curses'),
                array(
                    'player_name' => $this->game->getActivePlayerName(),
                    'amount' => 4,
                    'curses' => $curseTotal,
                    'curseTotal' => $curseTotal * -1,
                    'player' => $player->getUiData(),
                    'cards' => $this->game->cardManager->getUiDataFromCards($ringCards),
                )
            );
        }
    }
}