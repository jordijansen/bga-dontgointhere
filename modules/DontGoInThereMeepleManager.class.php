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
 * DontGoInThereMeepleManager.class.php
 * 
 * Functions to manage meeples
 */

require_once('DontGoInThereMeeple.class.php');

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

    // Map of hex colors to meeple type
    private static $hexToMeepleType = [
        '4c266d' => PURPLE,
        'e44e35' => RED,
        '1bad80' => TEAL,
        'ffffff' => WHITE,
        'f4af2b' => YELLOW,
    ];

    /**
     * Determine the type of meeple based on a player's hex color
     * @param int $hexColor A hex colot
     * @throws BgaVisibleSystemException 
     * @return int The meeple type value
     */
    private function determineMeepleType($hexColor)
    {
        if(!isset(self::$hexToMeepleType[$hexColor]))
        {
            throw new BgaVisibleSystemException("determineMeepleType: Unknown hex color $hexColor");
        }
        return self::$hexToMeepleType[$hexColor];
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

    public function getMeeplesInRoom($roomUiPosition)
    {
        $meeplesInRoom = self::getMeeples(ROOM_PREPEND . $roomUiPosition);
        return self::sortMeeplesByUiPosition($meeplesInRoom);
    }

    /**
     * Get the next meeple in line for room resolution
     * @param int $roomUiPosition UI position of room being resolved
     * @return DontGoInThereMeeple|bool The next meeple or false if none left
     */
    public function getTopMeepleInRoom($roomUiPosition)
    {
        $meeplesInRoom = self::getMeeples(ROOM_PREPEND . $roomUiPosition);

        if(count($meeplesInRoom) > 0) {
            $sortedMeeples = self::sortMeeplesByUiPosition($meeplesInRoom);
            return $sortedMeeples[0];
        }

        return false;
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
     * Sort a list of meeples in ascending order of ui position
     * @param array<DontGoInThereMeeple> $meeples Array of meeple objects
     * @return array<DontGoInThereMeeple> Sorted array of meeple objects
     */
    private function sortMeeplesByUiPosition($meeples)
    {
        usort($meeples, function(DontGoInThereMeeple $a, DontGoInThereMeeple $b) {
            // This shouldn't be possible, but checking just in case
            if($a->getUiPosition() === $b->getUiPosition()) {
                return 0;
            }
            return $a->getUiPosition() < $b->getUiPosition() ? -1 : 1;
        });

        return $meeples;
    }

    /**
     * Activate a meeple on a room
     * @param int $playerId ID of player's meepls
     * @param int $roomUiPosition ui position of room
     * @return DontGoInThereMeeple|bool
     */
    public function triggerMeeple($playerId, $roomUiPosition)
    {
        $meeple = self::getTopMeepleInRoom($roomUiPosition);
        if ($playerId == $meeple->getOwner()) {
            $this->meeples->moveCard($meeple->getId(), HAND);
        }
        return $meeple;
    }
}