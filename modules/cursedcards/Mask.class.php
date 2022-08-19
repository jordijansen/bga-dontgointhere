<?php

/**
 * Mask: a Mask Cursed Card object
 */
class Mask extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Mask');
        $this->type = MASK;
        $this->cssClass = "dgit-card-mask-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Mask
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('Pass 1 ghost per mask in your set to the rival on your right');
    }
}