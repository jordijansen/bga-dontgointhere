<?php

/**
 * Amulet: an Amulet Cursed Card object
 */
class Amulet extends DontGoInThereCursedCard
{
    public function __construct($game, $id, $typeArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Amulet');
        $this->type = AMULET;
        $this->cssClass = "dgit-card-amulet-".$typeArg;
        $this->tooltipText = self::buildTooltipText($typeArg);
        $this->curses = $typeArg;
        $this->diceIcons = self::determineDiceIcons($typeArg);
        $this->endGameTrigger = true;
    }

    /**
     * Build tooltip text for Amulet
     */
    private function buildTooltipText($typeArg)
    {
        if($typeArg == 4)
        {
            return "";
        }

        $dispelStrength = $typeArg + 1;

        return clienttranslate('Dispel an amulet with '.$dispelStrength.' curses');
    }
}