<?php

/**
 * A Mirror Cursed Card object
 */
class Mirror extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Mirror');
        $this->type = MIRROR;
        $this->cssClass = "dgit-card-mirror-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Mirror
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you collect a Mirror card, take 1 Ghost token. When you collect 3 Mirror cards, immediately dispel those 3 Mirror cards.');
    }
}