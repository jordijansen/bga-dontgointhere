<?php

/**
 * Ring: a Ring Cursed Card object
 */
class Ring extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Ring');
        $this->type = RING;
        $this->cssClass = "dgit-card-ring-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Ring
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('For every 4 rings: Dispel 4 rings');
    }
}