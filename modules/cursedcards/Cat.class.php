<?php

/**
 * A Cat Cursed Card object
 */
class Cat extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Cat');
        $this->type = CAT;
        $this->cssClass = "dgit-card-cat-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = true;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Cat
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('At game end, if you have 9 or fewer Ghost tokens, dispel all but 1 Cat card of your choice.');
    }
}