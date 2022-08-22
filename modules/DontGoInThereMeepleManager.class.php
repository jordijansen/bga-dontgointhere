<?php

require_once('DontGoInThereMeeple.class.php');

/**
 * Functions to manage meeples
 */
class DontGoInThereMeepleManager extends APP_GameClass
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;

        $this->meeples = $this->game->getNew("module.common.deck");
        $this->meeples->init("meeple");
    }

    /**
     * Setup meeples for a new game
     * @param array<DontGoInTherePlayer> $players List of player objects
     * @return void
     */
    public function setupNewGame($players)
    {
        // Create 5 meeples for each player
        $meeples = [];
        foreach($players as $player)
        {
            $meepleType = self::determineMeepleType($player->getColor());
            $meeples[] = [TYPE => $meepleType, TYPE_ARG => $player->getId(), 'nbr' => 5];
        }
        $this->meeples->createCards($meeples, HAND);
    }

    /**
     * Factory to create a DontGoInThereMeeple object
     * @param mixed $id Meeple ID from DB
     * @param mixed $type Meeple type from DB
     * @param mixed $typeArg Meeple typeArg from DB
     * @param mixed $locationArg Meeple locationArg from DB
     * @return DontGoInThereMeeple A DontGoInThereMeeple object
     */
    public function getMeeple($id, $type, $typeArg, $locationArg)
    {
        return new DontGoInThereMeeple($this->game, $id, $type, $typeArg, $locationArg);
    }

    /**
     * Get all DontGoInThereMeeple objects in a specified location
     * @param string $location Location value in DB
     * @return array<DontGoInThereMeeple> An array of DontGoInThereMeeple objects
     */
    public function getMeeples($location)
    {
        $meeples = $this->meeples->getCardsInLocation($location);
        return array_map(function($meeple) {
            return $this->getMeeple($meeple[ID], $meeple[TYPE], $meeple[TYPE_ARG], $meeple[LOCATION_ARG]);
        }, $meeples);
    }

    /**
     * Move a meeple into a room
     * @param DontGoInTherePlayer $player Player whose meeple is moving
     * @param int $room UiPosition of room
     * @param int $space Space on room meeple is moving to
     * @return void
     */
    public function moveMeepleToRoom($player, $room, $space)
    {
        $meeple = self::getMeepleFromHand($player);
        $this->meeples->moveCard($meeple->getId(), ROOM_PREPEND . $room, $space);
    }

    /**
     * Get ui data of all meeples in a specified location
     * @param string $location Location value in DB
     * @return array<mixed> An array of ui data for meeples
     */
    public function getUiData($location)
    {
        $ui = [];
        foreach($this->getMeeples($location) as $meeple)
        {
            $ui[] = $meeple->getUiData();
        }
        return $ui;
    }

    // Map of hex colors to meeple type
    private static $hexToMeepleType = [
        '451f69' => PURPLE,
        'f8602b' => RED,
        '00cbb1' => TEAL,
        'eaeaea' => WHITE,
        'fff97b' => YELLOW,
    ];

    private function determineMeepleType($hexColor)
    {
        if(!isset(self::$hexToMeepleType[$hexColor]))
        {
            throw new BgaVisibleSystemException("determineMeepleType: Unknown hex color $hexColor");
        }
        return self::$hexToMeepleType[$hexColor];
    }

    /**
     * Grab any of a player's unused meeples
     * @param DontGoInTherePlayer $player A player object
     * @return DontGoInThereMeeple A meeple object
     */
    private function getMeepleFromHand($player)
    {
        $meepleType = self::$hexToMeepleType[$player->getColor()];
        $meepleOwner = $player->getId();
        $playersMeeples = $this->meeples->getCardsOfTypeInLocation($meepleType, $meepleOwner, HAND);
        $meepleRecord = array_pop($playersMeeples);
        return self::getMeeple($meepleRecord['id'], $meepleRecord['type'], $meepleRecord['type_arg'], $meepleRecord['location_arg']);
    }
}