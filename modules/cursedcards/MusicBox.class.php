<?php

/**
 * MusicBox: a Music Box Cursed Card object
 */
class MusicBox extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Music Box');
        $this->type = MUSIC_BOX;
        $this->cssClass = "dgit-card-music-box-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = true;
    }

    /**
     * Build tooltip text for Music Box
     */
    private function buildTooltipText($typeArg)
    {
        return clienttranslate('Most curse value on music boxes: Dispel two music boxes');
    }
}