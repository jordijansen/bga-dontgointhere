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
 * Hallway.class.php
 * 
 * Hallway room object
 */
class Hallway extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Hallway');
        $this->type = HALLWAY;
        $this->cssClass = "dgit-room-hallway";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = BASEMENT;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Hallway
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('After rolling the dice for the Hallway, the player that placed the 3rd Meeple in the Hallway may change 1 die result.');
    }

    public function onPlacement($meeple) {}
}