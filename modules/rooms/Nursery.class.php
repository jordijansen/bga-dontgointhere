<?php

/**
 * A Nursery Room object
 */
class Nursery extends DontGoInThereRoom
{
    public function __construct($game, $id, $locationArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Nursery');
        $this->type = NURSERY;
        $this->cssClass = "dgit-room-nursery";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = ATTIC;
        $this->uiPosition = $locationArg;
    }

    /**
     * Build tooltip text for Nursery
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('If you place a Meeple on the bottom space of the Nursery, immediately discard a Ghost token.');
    }
}