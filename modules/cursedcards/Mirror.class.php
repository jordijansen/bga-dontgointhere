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
 * Mirror.class.php
 * 
 * Mirror card object
 */
class Mirror extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Mirror');
        $this->type = MIRROR;
        $this->cssClass = "dgit-card-mirror-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Mirror
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect a Mirror card, take 1 Ghost. When you collect 3 Mirror cards, immediately dispel those 3 Mirror cards.');
    }

    /**
     * Trigger effect of mirror
     * @param mixed $args
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];
        $this->game->playerManager->adjustPlayerGhosts($player->getId(), 1);
        $this->game->notifyAllPlayers(
            ADJUST_GHOSTS,    
            clienttranslate('${player_name} gains 1 Ghost from taking a Mirror'),
            array(
                'player_name' => $this->game->getActivePlayerName(),
                'playerId' => $player->getId(),
                'amount' => 1,
            )
        );

        $mirrorCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), MIRROR);
        if(count($mirrorCards) == 3) {
            $this->game->playerManager->adjustPlayerDispeled($player->getId(), 3);
            $curseTotal = 0;
            foreach($mirrorCards as $mirrorCard) {
                $curseTotal += $mirrorCard->getCurses();
            }
            $this->game->playerManager->adjustPlayerCurses($player->getId(), $curseTotal * -1);
            $this->game->cardManager->moveCards($mirrorCards, DISPELED);

            $this->game->notifyAllPlayers(
                DISPEL_CARDS,    
                clienttranslate('${player_name} dispels ${amount} Mirror cards '),
                array(
                    'player_name' => $this->game->getActivePlayerName(),
                    'amount' => 3,
                    'curseTotal' => $curseTotal * -1,
                    'player' => $player->getUiData(),
                    'cards' => $this->game->cardManager->getUiDataFromCards($mirrorCards),
                )
            );
        }
    }
}