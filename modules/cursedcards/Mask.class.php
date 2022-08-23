<?php

/**
 * A Mask Cursed Card object
 */
class Mask extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Mask');
        $this->type = MASK;
        $this->cssClass = "dgit-card-mask-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
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