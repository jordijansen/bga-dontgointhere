<?php

/**
 * Hallway: A Hallway Room object
 */
class Hallway extends DontGoInThereRoom
{
    public function __construct($game, $id)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Hallway');
        $this->type = HALLWAY;
        $this->cssClass = "dgit-room-hallway";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = BASEMENT;
    }

    /**
     * buildTooltipText: Build tooltip text for Hallway
     */
    private function buildTooltipText()
    {
        return clienttranslate('After rolling the dice for the Hallway, the player that placed the 3rd Meeple in the Hallway may change 1 die result.');
    }
}