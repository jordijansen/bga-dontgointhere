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
 * DontGoInThereRoomManager.class.php
 * 
 * Functions to manage rooms
 */

require_once('DontGoInThereRoom.class.php');
require_once('rooms/Attic.class.php');
require_once('rooms/Basement.class.php');
require_once('rooms/Hallway.class.php');
require_once('rooms/Library.class.php');
require_once('rooms/Nursery.class.php');
require_once('rooms/SecretPassage.class.php');

class DontGoInThereRoomManager extends APP_GameClass
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;

        $this->rooms = $this->game->getNew("module.common.deck");
        $this->rooms->init("room");
    }

    /**
     * Setup rooms for a new game
     * @return void
     */
    public function setupNewGame()
    {
        // Get list of room types
        $roomTypes = range(ATTIC, SECRET_PASSAGE);

        // Create 1 of each room
        $rooms = [];
        foreach($roomTypes as $roomType)
        {
            $rooms[] = [TYPE => $roomType, TYPE_ARG => 0, 'nbr' => 1];
        }
        $this->rooms->createCards($rooms, DECK);

        // Shuffle room deck
        $this->rooms->shuffle(DECK);

        // Deal 3 opening rooms
        for($uiPosition = 1; $uiPosition <= 3; $uiPosition++)
        {
            self::dealOpeningRoom($uiPosition);
        }
    }

    // Map of room type to object class
    private static $roomClasses = [
        ATTIC => 'Attic',
        BASEMENT => 'Basement',
        HALLWAY => 'Hallway',
        LIBRARY => 'Library',
        NURSERY => 'Nursery',
        SECRET_PASSAGE => 'SecretPassage',
    ];

    // Map of rooms and their flip sides
    private static $roomFlipSides = [
        ATTIC => NURSERY,
        BASEMENT => HALLWAY,
        HALLWAY => BASEMENT,
        LIBRARY => SECRET_PASSAGE,
        NURSERY => ATTIC,
        SECRET_PASSAGE => LIBRARY,
    ];

    /**
     * Deal a starting room, put is flipside in waiting.
     * @param int $roomPosition The UI position of a room within its location
     * @return void
     */
    private function dealOpeningRoom($roomPosition)
    {
        // Get room deck
        $rooms = self::getRooms('deck');

        // Pull first room
        $drawnRoom = array_shift($rooms);

        // Place card faceup in position
        $this->rooms->moveCard($drawnRoom->getId(), FACEUP, $roomPosition);

        // Get flip side room, and put in waiting
        $flipSideRoom = self::findRoomByType($rooms, $drawnRoom->getFlipSideRoom());
        $this->rooms->moveCard($flipSideRoom->getId(), FACEDOWN, $roomPosition);
    }

    /**
     * Return a DontGoInThereRoom of specified type from a list of rooms
     * @param array<DontGoInThereRoom> $rooms An array of DontGoInThereRoom objects
     * @param int $roomType A room type value
     * @return mixed a DontGoInThereRoom object if it exists in the list, otherwise false
     */
    public function findRoomByType($rooms, $roomType)
    {
        foreach($rooms as $room)
        {
            if($roomType == $room->getType()) {
                return $room;
            }
        }

        return false;
    }

    /**
     * Return a DontGoInThereRoom of specified ui location from a list of rooms
     * @param array<DontGoInThereRoom> $rooms An array of DontGoInThereRoom objects
     * @param int $uiPosition A room ui position
     * @return mixed a DontGoInThereRoom object if it exists in the list, otherwise false
     */
    public function findRoomByUiPosition($rooms, $uiPosition)
    {
        foreach($rooms as $room)
        {
            if($uiPosition == $room->getUiPosition()) {
                return $room;
            }
        }
        return false;
    }

    /**
     * Get a faceup room by its ui position
     * @param int $uiPosition
     * @return DontGoInThereRoom room object
     */
    public function getFaceupRoomByUiPosition($uiPosition)
    {
        return self::findRoomByUiPosition(self::getRooms(FACEUP), $uiPosition);
    }

    /**
     * Get the UI position of the Library if it is faceup
     * @return mixed UI position of Library if its faceup otherwise return false
     */
    public function getLibraryPosition()
    {
        $faceupRooms = self::getRooms(FACEUP);
        $library = self::findRoomByType($faceupRooms, LIBRARY);
        if(!$library){
            return 0;
        }
        return $library->getUiPosition();
    }

    /**
     * Factory to create a DontGoInThereRoom object
     * @param mixed $room Room record from databas
     * @throws BgaVisibleException 
     * @return DontGoInThereRoom A DontGoInThereRoom object
     */
    public function getRoom($room)
    {
        $roomType = $room[TYPE];
        if(!isset(self::$roomClasses[$roomType]))
        {
            throw new BgaVisibleException("getRoom: Unknown room type $roomType");
        }
        $className = self::$roomClasses[$roomType];
        return new $className($this->game, $room);
    }

    /**
     * Get all DontGoInThereRoom objects in specified location
     * @param string $location Location value in database
     * @return array<DontGoInThereRoom> An array of DontGoInThereRoom objects
     */
    public function getRooms($location)
    {
        $rooms = $this->rooms->getCardsInLocation($location);
        return array_map(function($room) {
            return $this->getRoom($room);
        }, $rooms);
    }

    /**
     * Get the game state value for the player who triggered room resolution
     * @return int Id of player who triggered current room resolution
     */
    public function getRoomResolver()
    {
        return $this->game->getGameStateValue(ROOM_RESOLVER);
    }

    /**
     * Get the game state value for the room currently being resolved
     * @return int Ui position of room being resolved
     */
    public function getRoomResolving()
    {
        return $this->game->getGameStateValue(ROOM_RESOLVING);
    }

    /**
     * Get the game state value for whether the secret passage card has been revealed to all players
     * @return int Boolean if secret passage has been revealed or not
     */
    public function getSecretPassageRevealed()
    {
        return $this->game->getGameStateValue(SECRET_PASSAGE_REVEALED);
    }

    /**
     * Get uiData of all rooms in specified location
     * @param string $location Location value from database
     * @return array An array of uiData for a room
     */
    public function getUiData($location)
    {
        $ui = [];
        foreach($this->getRooms($location) as $room)
        {
            $ui[] = $room->getUiData();
        }
        return $ui;
    }

    /**
     * Set the game state value for the player who triggered room resolution
     * @param int $playerId Id of player who triggered current room resolution
     */
    public function setRoomResolver($playerId)
    {
        $this->game->setGameStateValue(ROOM_RESOLVER, $playerId);
    }

    /**
     * Set the game state value for the room currently being resolved
     * @param int Ui position of room being resolved
     */
    public function setRoomResolving($roomUiPosition)
    {
        $this->game->setGameStateValue(ROOM_RESOLVING, $roomUiPosition);
    }

    /**
     * Set the game state value for whether the secret passage card has been revealed to all players
     * @return int Boolean if secret passage has been revealed or not
     */
    public function setSecretPassageRevealed($revealedStatus)
    {
        return $this->game->setGameStateValue(SECRET_PASSAGE_REVEALED, $revealedStatus);
    }
}