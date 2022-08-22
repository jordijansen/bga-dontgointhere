<?php

/**
 * A Library Room object
 */
class Library extends DontGoInThereRoom
{
    public function __construct($game, $id, $locationArg)
    {
        parent::__construct($game);
        $this->id = $id;
        $this->name = clienttranslate('Library');
        $this->type = LIBRARY;
        $this->cssClass = "dgit-room-library";
        $this->tooltipText = self::buildTooltipText();;
        $this->flipSideRoom = SECRET_PASSAGE;
        $this->uiPosition = $locationArg;

        self::onReveal();
    }

    /**
     * Sort cards in ascending order by curse value and then inital ui position
     * @return void
     */
    public function onReveal()
    {
        $libraryCards = $this->game->cardManager->getCursedCards('room_' . $this->uiPosition);
        
        usort($libraryCards, fn(DontGoInThereCursedCard $a, DontGoInThereCursedCard $b): int =>
            [$a->getCurses(), $a->getUiPosition()]
            <=>
            [$b->getCurses(), $b->getUiPosition()]
        );

        $newPosition = 1;
        foreach($libraryCards as $libraryCard) 
        {
            $this->game->cardManager->moveCard($libraryCard, 'room_' . $this->uiPosition, $newPosition);
            $newPosition = $newPosition + 1;
        }
    }

    /**
     * Build tooltip text for Library
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('Place the 3 Cursed Cards in the Library in ascending Curse value. If cards have the same Curse value, the one drawn first is placed to the left. When a player takes the leftmost card they must first take a Ghost token. When a player takes the rightmost card, they first discard a Ghost Token.');
    }
}