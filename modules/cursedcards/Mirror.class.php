<?php

/**
 * Mirror: A Mirror Cursed Card object
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
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * buildTooltipText: Build tooltip text for Mirror
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect a Mirror card, take 1 Ghost token. When you collect 3 Mirror cards, immediately dispel those 3 Mirror cards.');
    }
}