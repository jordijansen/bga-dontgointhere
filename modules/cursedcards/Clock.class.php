<?php

/**
 * Clock: a Clock Cursed Card object
 */
class Clock extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Clock');
        $this->type = CLOCK;
        $this->cssClass = "dgit-card-clock-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Clock
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('1st to 8 or more curse value on clocks dispels 2 clocks');
    }
}