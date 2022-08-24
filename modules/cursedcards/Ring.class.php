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
}