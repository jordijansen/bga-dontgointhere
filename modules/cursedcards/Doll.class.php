<?php

/**
 * Doll: a Doll Cursed Card object
 */
class Doll extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Doll');
        $this->type = DOLL;
        $this->cssClass = "dgit-card-doll-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Doll
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('Dispel set of dolls whose curse values add up to exactly 6');
    }
}