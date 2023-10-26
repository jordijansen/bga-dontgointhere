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
 * Amulet.class.php
 * 
 * Amulet card object
 */

class Amulet extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Amulet');
        $this->type = AMULET;
        $this->cssClass = "dgit-card-amulet-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText($row[TYPE_ARG]);
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = true;
        $this->uiPosition = $row[LOCATION_ARG];
        $this->statName = 'amulets';
    }

    /**
     * Build tooltip text for Amulet
     * Amulet dispel strength is 1 more than curse value, except for 4 which has no ability
     * @param int $curses Curse value of card
     * @return string Tooltip text
     */
    private function buildTooltipText($curses)
    {
        if($curses == 4)
        {
            return "";
        }

        $dispelStrength = $curses + 1;

        return sprintf(DontGoInThere::totranslate('At game end, dispel an Amulet card with a Curse value of %d.'), $dispelStrength);
    }
}