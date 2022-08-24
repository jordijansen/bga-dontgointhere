<?php

/**
 * A Library Room object
 */
class Library extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Library');
        $this->type = LIBRARY;
        $this->cssClass = "dgit-room-library";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = SECRET_PASSAGE;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Library
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('Place the 3 Cursed Cards in the Library in ascending Curse value. If cards have the same Curse value, the one drawn first is placed to the left. When a player takes the leftmost card they must first take a Ghost token. When a player takes the rightmost card, they first discard a Ghost Token.');
    }

    public function onPlacement($meeple){}
}