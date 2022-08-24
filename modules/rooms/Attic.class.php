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
 * Attic.class.php
 * 
 * Attic room object
 */

class Attic extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Attic');
        $this->type = ATTIC;
        $this->cssClass = "dgit-room-attic";
        $this->tooltipText = '';
        $this->flipSideRoom = NURSERY;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Attic
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('If you place a Meeple on the top space of the Attic immediately take a Ghost token.');
    }

    public function onPlacement($meeple){}
}