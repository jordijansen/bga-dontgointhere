<?php

/**
 * A DontGoInThereDie object
 */
class DontGoInThereDie extends APP_GameClass
{
    private $game;
    private $id;
    private $face;

    public function __construct($game, $row)
    {
        $this->game = $game;
        $this->id = (int) $row['id'];
        $this->face = (int) $row['face'];
    }

    public function getId() { return $this->id; }
    public function getFace() { return $this->face; }

    /**
     * Get die uiData visible by current player
     * @return array<mixed> An array of uiData for a die
     */
    public function getUiData()
    {
        return [
            'id' => $this->id,
            'face' => $this->face,
        ];
    }
}