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
 * Basement.class.php
 * 
 * Basement room object
 */

class Basement extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Basement');
        $this->type = BASEMENT;
        $this->cssClass = "dgit-room-basement";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = HALLWAY;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Basement
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('After rolling the dice for the Attic, the player that placed the 3rd Meeple in the Attic may re-roll the dice 1 time.');
    }

    public function onPlacement($meeple){}
}