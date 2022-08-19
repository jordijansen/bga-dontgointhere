<?php

/**
 * Library: A Library Room object
 */
class Library extends DontGoInThereRoom
{
    public function __construct($game, $id)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Library');
        $this->type = LIBRARY;
        $this->cssClass = "dgit-room-library";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = SECRET_PASSAGE;
    }

    /**
     * buildTooltipText: Build tooltip text for Library
     */
    private function buildTooltipText()
    {
        return clienttranslate('Place the 3 Cursed Cards in the Library in ascending Curse value. If cards have the same Curse value, the one drawn first is placed to the left. When a player takes the leftmost card they must first take a Ghost token. When a player takes the rightmost card, they first discard a Ghost Token.');
    }
}