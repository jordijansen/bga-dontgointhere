<?php

/**
 * Cat: A Cat Cursed Card object
 */
class Cat extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Cat');
        $this->type = CAT;
        $this->cssClass = "dgit-card-cat-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = true;
    }

    /**
     * buildTooltipText: Build tooltip text for Cat
     */
    private function buildTooltipText()
    {
        return clienttranslate('At game end, if you have 9 or fewer Ghost tokens, dispel all but 1 Cat card of your choice.');
    }
}