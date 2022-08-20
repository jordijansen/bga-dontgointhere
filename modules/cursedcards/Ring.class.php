<?php

/**
 * A Ring Cursed Card object
 */
class Ring extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg, $locationArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Ring');
        $this->type = RING;
        $this->cssClass = "dgit-card-ring-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
        $this->uiPosition = $locationArg;
    }

    /**
     * Build tooltip text for Ring
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect 4 Ring cards, immediately dispel those 4 Ring cards.');
    }
}