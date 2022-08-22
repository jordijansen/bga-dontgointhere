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
 * Functions to manage cards
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
     * Setup cards for a new game
     * @param int $playerCount Number of players in the game
     * @return void
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

        // Deal 3 cards cards to each room
        for($roomPosition = 1; $roomPosition <= 3; $roomPosition++)
        {
            for($cardSlot = 1; $cardSlot <= 3; $cardSlot++)
            {
                $this->cards->pickCardForLocation('deck', 'room_'.$roomPosition, $cardSlot);
            }
        }
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

    // Map of player counts to setup variables
    private static $playerCountVariables = [
        2 => ['cursedCardTypes' => 5, 'cardsToRemove' => 16],
        3 => ['cursedCardTypes' => 6, 'cardsToRemove' => 12],
        4 => ['cursedCardTypes' => 7, 'cardsToRemove' => 8],
        5 => ['cursedCardTypes' => 8, 'cardsToRemove' => 4],
    ];

    /**
     * Get a count of cards in specified location
     * @param string $location Database location value
     * @param int $locationArg Database locationArg value
     * @return int The number of cards in the locations
     */
    public function countCursedCards($location, $locationArg = null)
    {
        return $this->cards->countCardsInLocation($location, $locationArg);
    }

    /**
     * Factory to create a DontGoInThereCursedCard object
     * @param int $cardType Card type id
     * @param int $id Card database id
     * @param int $typeArg Database type_arg Used to denote curse value of card
     * @param int $locationArg Database location_arg Used to denote ui position of card within its location
     * @throws BgaVisibleSystemException 
     * @return DontGoInThereCursedCard A DontGoInThereCursedCard object
     */
    public function getCursedCard($cardType, $id, $typeArg, $locationArg)
    {
        if(!isset(self::$cursedCardClasses[$cardType]))
        {
            throw new BgaVisibleSystemException("getCursedCard: Unknown cursed card type $cardType");
        }
        $className = self::$cursedCardClasses[$cardType];
        return new $className($this->game, $id, $typeArg, $locationArg);
    }

    /**
     * Get all DontGoInThereCursedCard objects in a specified location
     * @param string $location Location value in database
     * @return array<DontGoInThereCursedCard> An array of DontGoInThereCursedCard objects
     */
    public function getCursedCards($location)
    {
        $cards = $this->cards->getCardsInLocation($location);
        return array_map(function($card) {
            return $this->getCursedCard($card['type'], $card['id'], $card['type_arg'], $card['location_arg']);
        }, $cards);
    }

    /**
     * Get ui data of all cards in specified location
     * @param string $location Location value in database
     * @return array<mixed> An array of ui data for CursedCards
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
     * Move a card to a new location
     * @param DontGoInThereCursedCard $card card object
     * @param string $location Destination location
     * @param int $locationArg Destination location arg
     * @return void
     */
    public function moveCard($card, $location, $locationArg)
    {
        $this->cards->moveCard($card->getId(), $location, $locationArg);
    }

    /**
     * Move multiple cards to a new location
     * @param array<DontGoInThereCursedCard> $cards array of card objects
     * @param mixed $location Destination location
     * @param mixed $locationArg Destination location arg
     * @return void
     */
    public function moveCards($cards, $location, $locationArg)
    {
        foreach($cards as $card)
        {
            self::moveCard($card, $location, $locationArg);
        }
    }

    /**
     * Get an array of randomized cursed card types for game based on player count
     * @param int $playerCount Number of players in the game
     * @return array An array of card types
     */
    private function randomizeCursedCardTypes($playerCount)
    {
        $numberOfTypes = self::$playerCountVariables[$playerCount]['cursedCardTypes'];

        $possibleTypes = range(AMULET, TWIN);
        shuffle($possibleTypes);

        return array_slice($possibleTypes, 0, $numberOfTypes);
    }
}