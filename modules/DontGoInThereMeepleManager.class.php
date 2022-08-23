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
     * @param mixed $row Meeple record from DB
     * @return DontGoInThereMeeple A DontGoInThereMeeple object
     */
    public function getMeeple($row)
    {
        return new DontGoInThereMeeple($this->game, $row);
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
            return $this->getMeeple($meeple);
        }, $meeples);
    }

    /**
     * Move a meeple into a room
     * @param DontGoInTherePlayer $player Player whose meeple is moving
     * @param int $room UiPosition of room
     * @param int $space Space on room meeple is moving to
     * @return DontGoInThereMeeple meeple object moved
     */
    public function moveMeepleToRoom($player, $room, $space)
    {
        $meeple = self::getMeepleFromHand($player);
        $this->meeples->moveCard($meeple->getId(), ROOM_PREPEND . $room, $space);
        $movedMeeple = $this->meeples->getCard($meeple->getId());
        return self::getMeeple($movedMeeple);
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
        '4c266d' => PURPLE,
        'e44e35' => RED,
        '1bad80' => TEAL,
        'ffffff' => WHITE,
        'f4af2b' => YELLOW,
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
        return self::getMeeple($meepleRecord);
    }
}