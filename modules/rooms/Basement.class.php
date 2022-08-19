<?php

/**
 * Basement: A Basement Room object
 */
class Basement extends DontGoInThereRoom
{
    public function __construct($game, $id)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Basement');
        $this->type = BASEMENT;
        $this->cssClass = "dgit-room-basement";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = HALLWAY;
    }

    /**
     * buildTooltipText: Build tooltip text for Basement
     */
    private function buildTooltipText()
    {
        return clienttranslate('After rolling the dice for the Attic, the player that placed the 3rd Meeple in the Attic may re-roll the dice 1 time.');
    }
}