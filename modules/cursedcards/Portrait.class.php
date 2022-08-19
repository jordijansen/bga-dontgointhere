<?php

/**
 * Portrair: A Portrait Cursed Card object
 */
class Portrait extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Portrait');
        $this->type = PORTRAIT;
        $this->cssClass = "dgit-card-portrait-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = true;
    }

    /**
     * buildTooltipText: Build tooltip text for Portrait
     */
    private function buildTooltipText()
    {
        return clienttranslate('At game end, dispel half of your Portrait cards (of your choice), rounded down.');
    }
}