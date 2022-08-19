<?php

/**
 * Tome: a Tome Cursed Card object
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
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Tome
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('For every 2 tomes: Dispel all cards of 1 type (not tomes)');
    }
}