<?php

require_once('DontGoInThereRoom.class.php');
require_once('rooms/Attic.class.php');
require_once('rooms/Basement.class.php');
require_once('rooms/Hallway.class.php');
require_once('rooms/Library.class.php');
require_once('rooms/Nursery.class.php');
require_once('rooms/SecretPassage.class.php');

/**
 * DontGoInThereRoomManager: Functions to manage rooms
 */
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
     * setupNewGame: Setup rooms for a new game
     */
    public function setupNewGame()
    {
        // Get list of room types
        $roomTypes = range(ATTIC, SECRET_PASSAGE);

        // Create 1 of each room
        $rooms = [];
        foreach($roomTypes as $roomType)
        {
            $rooms[] = ['type' => $roomType, 'type_arg' => 0, 'nbr' => 1];
        }
        $this->rooms->createCards($rooms, 'deck');

        // Shuffle room deck
        $this->rooms->shuffle('deck');

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
     * getRoom: Factory to create a room object
     */
    public function getRoom($roomType, $id)
    {
        if(!isset(self::$roomClasses[$roomType]))
        {
            throw new BgaVisibleException("getRoom: Unknown room type $roomType");
        }
        $className = self::$roomClasses[$roomType];
        return new $className($this->game, $id);
    }

    /**
     * getRooms: Get all rooms in specified location
     */
    public function getRooms($location)
    {
        $rooms = $this->rooms->getCardsInLocation($location);
        return array_map(function($room) {
            return $this->getRoom($room['type'], $room['id']);
        }, $rooms);
    }

    /**
     * getUiData: Get ui data of all rooms in specified location
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
     * dealOpeningRoom: Deal a starting room, put is flipside in waiting.
     */
    private function dealOpeningRoom($roomPosition)
    {
        // Get room deck
        $rooms = self::getRooms('deck');

        // Pull first room
        $drawnRoom = array_shift($rooms);

        // Place card faceup in position
        $this->rooms->moveCard($drawnRoom->getId(), 'faceup', $roomPosition);

        // Get flip side room, and put in waiting
        $flipSideRoom = self::findRoomByType($rooms, $drawnRoom->getFlipSideRoom());
        $this->rooms->moveCard($flipSideRoom->getId(), 'facedown', $roomPosition);
    }

    /**
     * findRoomByType: Return a room of specified type from a list of rooms
     */
    private function findRoomByType($rooms, $roomType)
    {
        foreach($rooms as $room)
        {
            if($roomType == $room->getType())
            {
                return $room;
            }
        }

        return false;
    }
}