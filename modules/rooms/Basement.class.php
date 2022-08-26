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
        $this->resolveAbility = true;
        $this->abilityText = self::buildAbilityText();
        $this->abilitySkipText = clienttranslate('skips re-rolling the dice');
    }

    /**
     * Build ability text for ability used in ability resolution phase
     * @return string Ability text
     */
    private function buildAbilityText()
    {
        return clienttranslate('may re-roll all the dice one time');
    }

    /**
     * Build tooltip text for Basement
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('After rolling the dice for the Basement, the player that placed the final Meeple in the Basement may re-roll the dice 1 time.');
    }
}