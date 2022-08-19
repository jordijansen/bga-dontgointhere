<?php

/**
 * Twin: a Twin Cursed Card object
 */
class Twin extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Twin');
        $this->type = TWIN;
        $this->cssClass = "dgit-card-twin-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * Build tooltip text for Twin
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('Dispel 2 identical twins');
    }
}