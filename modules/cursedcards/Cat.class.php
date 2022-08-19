<?php

/**
 * Cat: a Cat Cursed Card object
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
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = true;
    }

    /**
     * Build tooltip text for Cat
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('If you end with 9 or fewer ghosts dispel all but 1 cat');
    }
}