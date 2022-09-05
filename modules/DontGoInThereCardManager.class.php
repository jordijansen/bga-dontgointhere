<?php

/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereCardManager.class.php
 * 
 * Functions to manage cards
 */

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
    public function setupNewGame($playerCount, $libraryPosition)
    {
        // Get cursed card types to use for this game
        $cursedCardTypes = self::randomizeCursedCardTypes($playerCount);
        
        // Create 2 of each card
        $cards = [];
        foreach($cursedCardTypes as $cardType)
        {
            for($curses = 1; $curses <= 4; $curses++)
            {
                $cards[] = [TYPE => $cardType, TYPE_ARG => $curses, 'nbr' => 2];
            }
        }
        $this->cards->createCards($cards, DECK);

        // Shuffle deck
        $this->cards->shuffle(DECK);

        // Remove cards from deck based on player count
        $numberOfCardsToRemove = self::$playerCountVariables[$playerCount]['cardsToRemove'];
        $this->cards->pickCardsForLocation($numberOfCardsToRemove, DECK, TRASH);

        // Deal 3 cards cards to each room
        for($roomPosition = 1; $roomPosition <= 3; $roomPosition++)
        {
            // If this is the library we need to sort cards by curse value
            if($libraryPosition == $roomPosition) {
                $nextThreeCards = self::getCursedCardsOnTopOfDeck(3);

                $sortedCards = self::sortCardsByCurseValue($nextThreeCards);

                for($cardSlot = 1; $cardSlot <= 3; $cardSlot++)
                {
                    $nextCard = array_shift($sortedCards);
                    $this->cards->moveCard($nextCard->getId(), ROOM_PREPEND . $roomPosition, $cardSlot);
                }
            } else {
                for($cardSlot = 1; $cardSlot <= 3; $cardSlot++)
                {
                    $this->cards->pickCardForLocation(DECK, ROOM_PREPEND . $roomPosition, $cardSlot);
                }
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
     * Add up the dice icons that appear on the cards in a room
     * @param int $roomUiPosition UI position of room being checked
     * @return int Total dice icons on all cards in the room
     */
    public function countDiceIconsInRoom($roomUiPosition)
    {
        $cards = self::getCursedCards(ROOM_PREPEND . $roomUiPosition);
        $diceIcons = 0;
        foreach($cards as $card) 
        {
            $diceIcons += $card->getDiceIcons();
        }
        return $diceIcons;
    }
    
    /**
     * Draw three new cards for a room
     * @param DontGoInThereRoom $room room object
     */
    public function drawNewCardsForRoom($room)
    {
        if($room->getType() == LIBRARY) {
            $nextThreeCards = self::getCursedCardsOnTopOfDeck(3);

            $sortedCards = self::sortCardsByCurseValue($nextThreeCards);

            for($cardSlot = 1; $cardSlot <= 3; $cardSlot++)
            {
                $nextCard = array_shift($sortedCards);
                $this->cards->moveCard($nextCard->getId(), ROOM_PREPEND . $room->getUiPosition(), $cardSlot);
            }
        } else {
            for($cardSlot = 1; $cardSlot <= 3; $cardSlot++)
            {
                $this->cards->pickCardForLocation(DECK, ROOM_PREPEND . $room->getUiPosition(), $cardSlot);
            }
        }
    }

    /**
     * Factory to create a DontGoInThereCursedCard object
     * @param mixed $card DB record of card
     * @throws BgaVisibleSystemException 
     * @return DontGoInThereCursedCard A DontGoInThereCursedCard object
     */
    public function getCursedCard($card)
    {
        $cardType = $card['type'];
        if(!isset(self::$cursedCardClasses[$cardType]))
        {
            throw new BgaVisibleSystemException("getCursedCard: Unknown cursed card type $cardType");
        }
        $className = self::$cursedCardClasses[$cardType];
        return new $className($this->game, $card);
    }

    /**
     * Get a cursed card object from card id
     * @param int $cardId
     * @return DontGoInThereCursedCard
     */
    public function getCursedCardById($cardId)
    {
        return self::getCursedCard($this->cards->getCard($cardId));
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
            return $this->getCursedCard($card);
        }, $cards);
    }

    /**
     * Get X number of cards from the top of the deck
     * @param int $amount Number of cards to get
     * @return array<DontGoInThereCursedCard> Array of card objects
     */
    public function getCursedCardsOnTopOfDeck($amount)
    {
        $cards = $this->cards->getCardsOnTop($amount, DECK);
        return array_map(function($card) {
            return $this->getCursedCard($card);
        }, $cards);
    }

    /**
     * Get the last card selected by a player
     * @return DontGoInThereCursedCard
     */
    public function getLastSelectedCard()
    {
        return self::getCursedCardById($this->game->getGameStateValue(LAST_SELECTED_CARD));
    }

    /**
     * Get all cards of a type held by a player
     * @param int $playerId
     * @param int $type
     * @return array
     */
    public function getPlayerCardsOfType($playerId, $type) {
        $cards = $this->cards->getCardsOfTypeInLocation($type, null, HAND, $playerId);
        return array_map(function($card) {
            return $this->getCursedCard($card);
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
     * Get ui data of cards in a list
     * @param array<DontGoInThereCard> $cards list of card objects
     * @return array
     */
    public function getUiDataFromCards($cards)
    {
        $ui = [];
        foreach($cards as $card)
        {
            $ui[] = $card->getUiData();
        }
        return $ui;
    }

    /**
     * Check if card is in list
     * @param array<DontGoInThereCursedCard> $cards cards in a list
     * @param int $curseValue The value of card to find
     * @param mixed $existingId The id of a previously found card so we don't find it again
     * @return mixed
     */
    public function listContainsCard($cards, $curseValue, $existingId)
    {
        foreach($cards as $card)
        {
            if($card->getId() != $existingId && $card->getCurses() == $curseValue) {
                return $card;
            }
        }
        return false;
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
    public function moveCards($cards, $location, $locationArg = 0)
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

    /**
     * Store the id of the last card selected by a player
     * @param int $cardId
     * @return void
     */
    public function setLastSelectedCard($cardId)
    {
        $this->game->setGameStateValue(LAST_SELECTED_CARD, $cardId);
    }

    /**
     * Sort cards by curse value in ascending order
     * @param array<DontGoInThereCursedCard> $cards Array of cards to be sorted
     * @return array<DontGoInThereCursedCard> Sorted array of cards
     */
    public function sortCardsByCurseValue($cards)
    {
        usort($cards, function(DontGoInThereCursedCard $a, DontGoInThereCursedCard $b) {
            if($a->getCurses() === $b->getCurses()) {
                return 0;
            }
            return $a->getCurses() < $b->getCurses() ? -1 : 1;
        });

        return $cards;
    }

    /**
     * Sort cards by curse value in descending order
     * @param array<DontGoInThereCursedCard> $cards Array of cards to be sorted
     * @return array<DontGoInThereCursedCard> Sorted array of cards
     */
    public function sortCardsByCurseValueDesc($cards)
    {
        usort($cards, function(DontGoInThereCursedCard $a, DontGoInThereCursedCard $b) {
            if($a->getCurses() === $b->getCurses()) {
                return 0;
            }
            return $a->getCurses() > $b->getCurses() ? -1 : 1;
        });

        return $cards;
    }

    /**
     * A player takes a card from a romm
     * @param int $cardId Id of card being taken
     * @param DontGoInTherePlayer $player player taking the card
     * @return DontGoInThereCursedCard
     */
    public function takeCardFromRoom($cardId, $player) {
        $card = self::getCursedCardById($cardId);
        self::moveCard($card, HAND, $player->getId());
        return self::getCursedCard($this->cards->getCard($cardId));
    }


    /**
     * Trigger the effects of the cards with end game abilities
     * @return void
     */
    public function triggerEndGameEffects()
    {
        self::triggerAmuletEffect();
        self::triggerCatEffect();
        self::triggerMusicBoxEffect();
        self::triggerPortraitEffect();
    }

    /**
     * Trigger the end game effect of the Amulet
     * @return void
     */
    public function triggerAmuletEffect()
    {
        $players = $this->game->playerManager->getPlayers();

        foreach($players as $player)
        {
            $amuletCards = self::getPlayerCardsOfType($player->getId(), AMULET);
            if(count($amuletCards) > 0) {
                $oneAmulet = false;
                $anotherOneAmulet = false;
                $twoAmulet = false;
                $anotherTwoAmulet = false;
                $threeAmulet = false;
                $anotherThreeAmulet = false;
                $fourAmulet = false;
                $anotherFourAmulet = false;
                $totalCurseValue = 0;
                $cardsToDispel = [];

                $threeAmulet = self::listContainsCard($amuletCards, 3, null);
                $fourAmulet = self::listContainsCard($amuletCards, 4, null);
                if($threeAmulet != false && $fourAmulet != false){
                    $cardsToDispel[] = $fourAmulet;
                    $totalCurseValue = $totalCurseValue + 4;
                    $anotherThreeAmulet = self::listContainsCard($amuletCards, 3, $threeAmulet->getId());
                    $anotherFourAmulet = self::listContainsCard($amuletCards, 4, $fourAmulet->getId());
                    if($anotherThreeAmulet != false && $anotherFourAmulet != false) {
                        $cardsToDispel[] = $anotherFourAmulet;
                        $totalCurseValue = $totalCurseValue + 4;
                    }
                }

                $twoAmulet = self::listContainsCard($amuletCards, 2, null);
                if($twoAmulet != false && $threeAmulet != false) {
                    $cardsToDispel[] = $threeAmulet;
                    $anotherTwoAmulet = self::listContainsCard($amuletCards, 2, $twoAmulet->getId());
                    $anotherThreeAmulet = self::listContainsCard($amuletCards, 3, $threeAmulet->getId());
                    $totalCurseValue = $totalCurseValue + 3;
                    if($anotherTwoAmulet != false && $anotherThreeAmulet != false) {
                        $cardsToDispel[] = $anotherThreeAmulet;
                        $totalCurseValue = $totalCurseValue + 3;
                    }
                }

                $oneAmulet = self::listContainsCard($amuletCards, 1, null);
                if($oneAmulet != false && $twoAmulet != false) {
                    $cardsToDispel[] = $twoAmulet;
                    $totalCurseValue = $totalCurseValue + 2;
                    $anotherOneAmulet = self::listContainsCard($amuletCards, 1, $oneAmulet->getId());
                    $anotherTwoAmulet = self::listContainsCard($amuletCards, 2, $twoAmulet->getId());
                    if($anotherOneAmulet != false && $anotherTwoAmulet != false) {
                        $cardsToDispel[] = $anotherTwoAmulet;
                        $totalCurseValue = $totalCurseValue + 2;
                    }
                }

                if(count($cardsToDispel) > 0) {
                    $this->game->playerManager->adjustPlayerDispeled($player->getId(), count($cardsToDispel));
                    $this->game->playerManager->adjustPlayerCurses($player->getId(), $totalCurseValue * -1);
                    $this->game->incStat($totalCurseValue * -1, 'amulets_curses', $player->getId());
                    $this->game->incStat(count($cardsToDispel), 'amulets_dispeled', $player->getId());
                    self::moveCards($cardsToDispel, DISPELED);
                    $this->game->notifyAllPlayers(
                        DISPEL_CARDS,    
                        clienttranslate('${player_name} dispels ${amount} Amulet ${plural} worth a total of ${curses} Curses'),
                        array(
                            'player_name' => $this->game->playerManager->getPlayerNameColorDiv($player),
                            'amount' => count($cardsToDispel),
                            'plural' => count($cardsToDispel) == 1 ? clienttranslate('card') : clienttranslate('cards'),
                            'curses' => $totalCurseValue,
                            'curseTotal' => $totalCurseValue * -1,
                            'player' => $player->getUiData(),
                            'cards' => $this->game->cardManager->getUiDataFromCards($cardsToDispel),
                        )
                    );
                }
            }
        }
    }

    /**
     * Trigger the end game effect of the Cat
     * @return void
     */
    public function triggerCatEffect()
    {
        $players = $this->game->playerManager->getPlayers();

        foreach($players as $player) 
        {
            $catCards = self::getPlayerCardsOfType($player->getId(), CAT);
            if(count($catCards) > 0 && $player->getGhostTokens() <= 9) {
                $sortedCats = self::sortCardsByCurseValue($catCards);
                array_shift($sortedCats);
                if(count($sortedCats) > 0) {
                    $totalCurseValue = 0;
                    foreach($sortedCats as $cat) {
                        $totalCurseValue = $totalCurseValue + $cat->getCurses();
                    }
                    $this->game->playerManager->adjustPlayerDispeled($player->getId(), count($sortedCats));
                    $this->game->playerManager->adjustPlayerCurses($player->getId(), $totalCurseValue * -1);
                    $this->game->incStat($totalCurseValue * -1, 'cats_curses', $player->getId());
                    $this->game->incStat(count($sortedCats), 'cats_dispeled', $player->getId());
                    self::moveCards($sortedCats, DISPELED);
                    $this->game->notifyAllPlayers(
                        DISPEL_CARDS,    
                        clienttranslate('${player_name} dispels ${amount} Cat ${plural} worth ${curses} Curses'),
                        array(
                            'player_name' => $this->game->playerManager->getPlayerNameColorDiv($player),
                            'amount' => count($sortedCats),
                            'plural' => count($sortedCats) == 1 ? clienttranslate('card') : clienttranslate('cards'),
                            'curses' => $totalCurseValue,
                            'curseTotal' => $totalCurseValue * -1,
                            'player' => $player->getUiData(),
                            'cards' => $this->game->cardManager->getUiDataFromCards($sortedCats),
                        )
                    );
                }
            }
        }
    }

    /**
     * Trigger the end game effect of the Music Box
     * @return void
     */
    public function triggerMusicBoxEffect()
    {
        $players = $this->game->playerManager->getPlayers();
        $maxCurses = 0;
        $playersWithMostCurses = [];

        // Figure out which player(s) have Music Boxes with the highest curse value
        foreach($players as $player) 
        {
            $musicBoxCards = self::getPlayerCardsOfType($player->getId(), MUSIC_BOX);
            $curseCount = 0;
            foreach($musicBoxCards as $musicBoxCard) {
                $curseCount = $curseCount + $musicBoxCard->getCurses();
            }

            if($curseCount > $maxCurses) {
                $maxCurses = $curseCount;
                $playersWithMostCurses = [];
                $playersWithMostCurses[] = $player;
            } else if($curseCount != 0 && $curseCount == $maxCurses) {
                $playersWithMostCurses[] = $player;
            }
        }

        // Those players dispel (up to) 2 Music Boxes
        foreach($playersWithMostCurses as $player)
        {
            $musicBoxCards = self::getPlayerCardsOfType($player->getId(), MUSIC_BOX);
            $sortedCards = self::sortCardsByCurseValueDesc($musicBoxCards);
            $cardsToDispel = [];

            if(count($sortedCards) >= 2) {
                $cardsToDispel[] = array_shift($sortedCards);
                $cardsToDispel[] = array_shift($sortedCards);
            } else if(count($sortedCards) == 1) {
                $cardsToDispel = $sortedCards;
            }

            $totalCurseValue = 0;
            foreach($cardsToDispel as $card) {
                $totalCurseValue = $totalCurseValue + $card->getCurses();
            }

            $this->game->playerManager->adjustPlayerDispeled($player->getId(), count($cardsToDispel));
            $this->game->playerManager->adjustPlayerCurses($player->getId(), $totalCurseValue * -1);
            $this->game->incStat($totalCurseValue * -1, 'music_boxes_curses', $player->getId());
            $this->game->incStat(count($cardsToDispel), 'music_boxes_dispeled', $player->getId());
            self::moveCards($cardsToDispel, DISPELED);
            $this->game->notifyAllPlayers(
                DISPEL_CARDS,    
                clienttranslate('${player_name} dispels ${amount} Music Box ${plural} worth a total of ${curses} Curses'),
                array(
                    'player_name' => $this->game->playerManager->getPlayerNameColorDiv($player),
                    'amount' => count($cardsToDispel),
                    'plural' => count($cardsToDispel) == 1 ? clienttranslate('card') : clienttranslate('cards'),
                    'curses' => $totalCurseValue,
                    'curseTotal' => $totalCurseValue * -1,
                    'player' => $player->getUiData(),
                    'cards' => $this->game->cardManager->getUiDataFromCards($cardsToDispel),
                )
            );
        }
    }

    /**
     * Trigger the end game effect of the Music Box
     * @return void
     */
    public function triggerPortraitEffect()
    {
        $players = $this->game->playerManager->getPlayers();

        foreach($players as $player)
        {
            $portraitCards = self::getPlayerCardsOfType($player->getId(), PORTRAIT);
            if(count($portraitCards) >= 2) {
                $numberToDispel = floor(count($portraitCards) / 2);
                $sortedCards = self::sortCardsByCurseValueDesc($portraitCards);
                $totalCurseValue = 0;
                $cardsToDispel = [];
                for($i = 0; $i < $numberToDispel; $i++) {
                    $nextCard = array_shift($sortedCards);
                    $totalCurseValue = $totalCurseValue + $nextCard->getCurses();
                    $cardsToDispel[] = $nextCard;
                }

                $this->game->playerManager->adjustPlayerDispeled($player->getId(), count($cardsToDispel));
                $this->game->playerManager->adjustPlayerCurses($player->getId(), $totalCurseValue * -1);
                $this->game->incStat($totalCurseValue * -1, 'portraits_curses', $player->getId());
                $this->game->incStat(count($cardsToDispel), 'portraits_dispeled', $player->getId());
                self::moveCards($cardsToDispel, DISPELED);
                $this->game->notifyAllPlayers(
                    DISPEL_CARDS,    
                    clienttranslate('${player_name} dispels ${amount} Portrait ${plural} worth a total of ${curses} Curses'),
                    array(
                        'player_name' => $this->game->playerManager->getPlayerNameColorDiv($player),
                        'amount' => count($cardsToDispel),
                        'plural' => count($cardsToDispel) == 1 ? clienttranslate('card') : clienttranslate('cards'),
                        'curses' => $totalCurseValue,
                        'curseTotal' => $totalCurseValue * -1,
                        'player' => $player->getUiData(),
                        'cards' => $this->game->cardManager->getUiDataFromCards($cardsToDispel),
                    )
                );
            }         
        }
    }

    /**
     * Checks if the conditions to trigger the tome are in effect
     * @param int $playerId
     * @return bool
     */
    public function triggerTome($playerId)
    {
        $tomeCards = self::getPlayerCardsOfType($playerId, TOME);
        // If player has a number of tomes divisible by two and has other cards to dispel
        if(count($tomeCards) % 2 == 0 && self::countCursedCards(HAND, $playerId) > count($tomeCards)) {
            return true;
        }
        return false;
    }
}