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
 * DontGoInThereMeeple.class.php
 * 
 * Meeple object
 */

class DontGoInThereMeeple extends APP_GameClass
{
    private $game;
    private $id;
    private $type;
    private $owner;
    private $cssClass;
    private $uiPosition;

    /**
     * Construct a DontGoInThereMeeple object
     * @param mixed $game The game class
     * @param mixed $row Meeple record from database
     */
    public function __construct($game, $row)
    {
        $this->game = $game;

        $this->id = $row[ID];
        $this->type = $row[TYPE];
        $this->owner = $row[TYPE_ARG];
        $this->cssClass = self::determineCssClass($row[TYPE]);
        $this->uiPosition = $row[LOCATION_ARG];
    }

    public function getId() { return $this->id; }
    public function getType() { return $this->type; }
    public function getOwner() { return $this->owner; }
    public function getCssClass() { return $this->cssClass; }
    public function getUiPosition() { return $this->uiPosition; }

    /**
     * Get meeple uiData
     * @return array<mixed> An array of ui data for a meeple
     */
    public function getUiData()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'owner' => $this->owner,
            'cssClass' => $this->cssClass,
            'uiPosition' => $this->uiPosition,
        ];
    }

    // Map of meeple type to CSS classes
    private static $typeToCssClass = [
        PURPLE => 'dgit-meeple-purple',
        RED => 'dgit-meeple-red',
        TEAL => 'dgit-meeple-teal',
        WHITE => 'dgit-meeple-white',
        YELLOW => 'dgit-meeple-yellow',
    ];

    /**
     * Determine meeple css class
     * @param int $meepleType The type (aka color) of the meeple
     * @throws BgaVisibleException 
     * @return string The css class of the meeple
     */
    private function determineCssClass($meepleType)
    {
        if(!isset(self::$typeToCssClass[$meepleType]))
        {
            throw new BgaVisibleException("determineCssClass: Unknown meeple type $meepleType");
        }
        return self::$typeToCssClass[$meepleType];
    }
}