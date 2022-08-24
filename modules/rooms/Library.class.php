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
 * Library.class.php
 * 
 * Library room object
 */

class Library extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Library');
        $this->type = LIBRARY;
        $this->cssClass = "dgit-room-library";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = SECRET_PASSAGE;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Library
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('Place the 3 Cursed Cards in the Library in ascending Curse value. If cards have the same Curse value, the one drawn first is placed to the left. When a player takes the leftmost card they must first take a Ghost token. When a player takes the rightmost card, they first discard a Ghost Token.');
    }

    public function onPlacement($meeple){}
}