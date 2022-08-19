<?php

/**
 * Portrair: a Portrait Cursed Card object
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
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = true;
    }

    /**
     * Build tooltip text for Portrait
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('For every 2 portraits: Dispel 1 portrait');
    }
}