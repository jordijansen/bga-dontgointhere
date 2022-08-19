<?php

/**
 * Mirror: a Mirror Cursed Card object
 */
class Mirror extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Mirror');
        $this->type = MIRROR;
        $this->cssClass = "dgit-card-mirror-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Mirror
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('Take 1 ghost. If you have 3 mirrors: Dispel three mirrors');
    }
}