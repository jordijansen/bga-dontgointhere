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
            $meeples[] = ['type' => $meepleType, 'type_arg' => $player->getId(), 'nbr' => 5];
        }
        $this->meeples->createCards($meeples, 'hand');
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
            return $this->getMeeple($meeple['id'], $meeple['type'], $meeple['type_arg'], $meeple['location_arg']);
        }, $meeples);
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
}