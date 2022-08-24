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
 * Doll.class.php
 * 
 * Doll card object
 */

class Doll extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Doll');
        $this->type = DOLL;
        $this->cssClass = "dgit-card-doll-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Doll
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you have a set of Doll cards whose Curse values add up to exactly 6, immediatelty dispel those Doll cards.');
    }
}