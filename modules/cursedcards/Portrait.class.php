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
 * Portrait.class.php
 * 
 * Portrait card object
 */

class Portrait extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Portrait');
        $this->type = PORTRAIT;
        $this->cssClass = "dgit-card-portrait-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = true;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Portrait
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('At game end, dispel half of your Portrait cards, rounded down.');
    }
}