<?php

/**
 * Tome: A Tome Cursed Card object
 */
class Tome extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Tome');
        $this->type = TOME;
        $this->cssClass = "dgit-card-tome-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * buildTooltipText: Build tooltip text for Tome
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect 2 Tome cards, choose a type (not Tomes) and immediately dispel all your cards of that type.');
    }
}