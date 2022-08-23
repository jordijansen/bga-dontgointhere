<?php

/**
 * Doll: A Doll Cursed Card object
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