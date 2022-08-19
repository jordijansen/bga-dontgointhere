<?php

/**
 * Attic: An Attic Room object
 */
class Attic extends DontGoInThereRoom
{
    public function __construct($game, $id)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Attic');
        $this->type = ATTIC;
        $this->cssClass = "dgit-room-attic";
        $this->tooltipText = '';
        $this->flipSideRoom = NURSERY;
    }

    /**
     * buildTooltipText: Build tooltip text for Attic
     */
    private function buildTooltipText()
    {
        return clienttranslate('If you place a Meeple on the top space of the Attic immediately take a Ghost token.');
    }
}