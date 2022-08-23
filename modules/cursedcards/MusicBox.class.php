<?php

/**
 * A Music Box Cursed Card object
 */
class MusicBox extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Music Box');
        $this->type = MUSIC_BOX;
        $this->cssClass = "dgit-card-music-box-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = true;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Music Box
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('At game end, if you have the most Curses on Music Box cards, dispel 2 Music Box cards of your choice. If tied, all tied players dispel 2 Music Box cards.');
    }
}