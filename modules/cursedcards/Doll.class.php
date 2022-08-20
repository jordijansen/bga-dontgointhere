<?php

/**
 * Doll: A Doll Cursed Card object
 */
class Doll extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg, $locationArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Doll');
        $this->type = DOLL;
        $this->cssClass = "dgit-card-doll-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
        $this->uiPosition = $locationArg;
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