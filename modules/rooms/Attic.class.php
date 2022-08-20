<?php

/**
 * An Attic Room object
 */
class Attic extends DontGoInThereRoom
{
    public function __construct($game, $id, $locationArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Attic');
        $this->type = ATTIC;
        $this->cssClass = "dgit-room-attic";
        $this->tooltipText = '';
        $this->flipSideRoom = NURSERY;
        $this->uiPosition = $locationArg;
    }

    /**
     * Build tooltip text for Attic
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('If you place a Meeple on the top space of the Attic immediately take a Ghost token.');
    }
}