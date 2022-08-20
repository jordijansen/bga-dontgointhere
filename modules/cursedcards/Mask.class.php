<?php

/**
 * A Mask Cursed Card object
 */
class Mask extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg, $locationArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Mask');
        $this->type = MASK;
        $this->cssClass = "dgit-card-mask-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
        $this->uiPosition = $locationArg;
    }

    /**
     * Build tooltip text for Mask
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect a Mask, immediately pass 1 Ghost token per Mask card in your set to the player to your right.');
    }
}