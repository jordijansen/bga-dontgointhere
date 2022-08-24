<?php

/**
 * A Hallway Room object
 */
class Hallway extends DontGoInThereRoom
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Hallway');
        $this->type = HALLWAY;
        $this->cssClass = "dgit-room-hallway";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = BASEMENT;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Hallway
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('After rolling the dice for the Hallway, the player that placed the 3rd Meeple in the Hallway may change 1 die result.');
    }

    public function onPlacement($meeple) {}
}