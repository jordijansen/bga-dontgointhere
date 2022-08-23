<?php

/**
 * An Amulet Cursed Card object
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

        return clienttranslate('At game end, dispel an amulet card with a Curse value of '.$dispelStrength.'.');
    }
}