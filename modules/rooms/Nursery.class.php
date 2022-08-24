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
 * Nursery.class.php
 * 
 * Nursery room object
 */

class Nursery extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Nursery');
        $this->type = NURSERY;
        $this->cssClass = "dgit-room-nursery";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = ATTIC;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Nursery
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('If you place a Meeple on the bottom space of the Nursery, immediately discard a Ghost token.');
    }

    public function onPlacement($meeple)
    {
    }
}