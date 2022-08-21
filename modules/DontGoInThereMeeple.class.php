<?php

/**
 * A DontGoInThereMeeple object
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
     * @param mixed $id Meeple ID from DB
     * @param mixed $type Meeple type from DB
     * @param mixed $typeArg Meeple typeArg from DB (aka owner's player id)
     * @param mixed $locationArg Meeple locationArg from DB
     */
    public function __construct($game, $id, $type, $typeArg, $locationArg)
    {
        $this->game = $game;

        $this->id = $id;
        $this->type = $type;
        $this->owner = $typeArg;
        $this->cssClass = self::determineCssClass($type);
        $this->uiPosition = $locationArg;
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