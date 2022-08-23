<?php

/**
 * A Portrait Cursed Card object
 */
class Portrait extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Portrait');
        $this->type = PORTRAIT;
        $this->cssClass = "dgit-card-portrait-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = true;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Portrait
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('At game end, dispel half of your Portrait cards (of your choice), rounded down.');
    }
}