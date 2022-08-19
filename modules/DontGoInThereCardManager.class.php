<?php

require_once('DontGoInThereCursedCard.class.php');
require_once('cursedcards/Amulet.class.php');
require_once('cursedcards/Cat.class.php');
require_once('cursedcards/Clock.class.php');
require_once('cursedcards/Doll.class.php');
require_once('cursedcards/HolyWater.class.php');
require_once('cursedcards/Mask.class.php');
require_once('cursedcards/Mirror.class.php');
require_once('cursedcards/MusicBox.class.php');
require_once('cursedcards/Portrait.class.php');
require_once('cursedcards/Ring.class.php');
require_once('cursedcards/Tome.class.php');
require_once('cursedcards/Twin.class.php');

/**
 * DontGoInThereCardManager: functions to manage cards
 */
class DontGoInThereCardManager extends APP_GameClass
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;

        $this->cards = $this->game->getNew("module.common.deck");
        $this->cards->init("card");
    }

    /**
     * setupNewGame: Setup cards for a new game
     */
    public function setupNewGame($playerCount)
    {
        // Get cursed card types to use for this game
        $cursedCardTypes = self::randomizeCursedCardTypes($playerCount);
        
        // Create 2 of each card
        $cards = [];
        foreach($cursedCardTypes as $cardType)
        {
            for($curses = 1; $curses <= 4; $curses++)
            {
                $cards[] = ['type' => $cardType, 'type_arg' => $curses, 'nbr' => 2];
            }
        }
        $this->cards->createCards($cards, 'deck');

        // Shuffle deck
        $this->cards->shuffle('deck');

        // Remove cards from deck based on player count
        $numberOfCardsToRemove = self::$playerCountVariables[$playerCount]['cardsToRemove'];
        $this->cards->pickCardsForLocation($numberOfCardsToRemove, 'deck', 'trash');
    }

    // Map of card type to object class
    private static $cursedCardClasses = [
        AMULET => 'Amulet',
        CAT => 'Cat',
        CLOCK => 'Clock',
        DOLL => 'Doll',
        HOLY_WATER => 'HolyWater',
        MASK => 'Mask',
        MIRROR => 'Mirror',
        MUSIC_BOX => 'MusicBox',
        PORTRAIT => 'Portrait',
        RING => 'Ring',
        TOME => 'Tome',
        TWIN => 'Twin',
    ];

    private static $playerCountVariables = [
        2 => ['cursedCardTypes' => 5, 'cardsToRemove' => 16],
        3 => ['cursedCardTypes' => 6, 'cardsToRemove' => 12],
        4 => ['cursedCardTypes' => 7, 'cardsToRemove' => 8],
        5 => ['cursedCardTypes' => 8, 'cardsToRemove' => 4],
    ];

    /**
     * getCursedCard: Factory to create a cursed card object
     */
    public function getCursedCard($cardType, $id, $typeArg)
    {
        if(!isset(self::$cursedCardClasses[$cardType]))
        {
            throw new BgaVisibleSystemException("getCursedCard: Unknown cursed card type $cardType");
        }
        $className = self::$cursedCardClasses[$cardType];
        return new $className($this->game, $id, $typeArg);
    }

    /**
     * getCursedCards: Get all cursed cards in specified location
     */
    public function getCursedCards($location)
    {
        $cards = $this->cards->getCardsInLocation($location);
        return array_map(function($card) {
            return $this->getCursedCard($card['type'], $card['id'], $card['type_arg']);
        }, $cards);
    }

    /**
     * getUiData: Get ui data of all cards in specified location
     */
    public function getUiData($location)
    {
        $ui = [];
        foreach($this->getCursedCards($location) as $card)
        {
            $ui[] = $card->getUiData();
        }
        return $ui;
    }

    /**
     * randomizeCursedCardTypes: Get an array of randomized cursed card types for game based on player count
     */
    private function randomizeCursedCardTypes($playerCount)
    {
        $numberOfTypes = self::$playerCountVariables[$playerCount]['cursedCardTypes'];

        $possibleTypes = range(AMULET, TWIN);
        shuffle($possibleTypes);

        return array_slice($possibleTypes, 0, $numberOfTypes);
    }
}