<?php

/**
 * HolyWater: A Holy Water Cursed Card object
 */
class HolyWater extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Holy Water');
        $this->type = HOLY_WATER;
        $this->cssClass = "dgit-card-holy-water-".$typeArg;
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = false;
    }

    /**
     * buildTooltipText: Build tooltip text for Holy Water
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect 2 Holy Water cards, immediately discard half of your Ghost tokens, rounded down.');
    }
}