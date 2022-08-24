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
 * DontGoInThereDie.class.php
 * 
 * Die object
 */

class DontGoInThereDie extends APP_GameClass
{
    private $game;
    private $id;
    private $value;
    private $face;
    private $cssClass;

    /**
     * Construct a DontGoInThereDie object
     * @param mixed $game The game class
     * @param mixed $row The die row from the database
     */
    public function __construct($game, $row)
    {
        $this->game = $game;

        $this->id = (int) $row['id'];
        $this->value = (int) $row['value'];
        $this->face = self::determineFace($this->value);
        $this->cssClass = self::determineCssClass($this->value);
    }

    public function getId() { return $this->id; }
    public function getValue() { return $this->value; }
    public function getFace() { return $this->face; }
    public function getCssClass() { return $this->cssClass; }

    /**
     * Get die uiData visible by current player
     * @return array<mixed> An array of uiData for a die
     */
    public function getUiData()
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'face' => $this->face,
            'cssClass' => $this->cssClass,
        ];
    }

    /**
     * Determine if a die is hidden, blank, or a ghost
     * @param int $value Numerical value of side of D6
     * @return int Key value for dice face
     */
    private function determineFace($value)
    {
        if($value == 0) {
            return HIDDEN;
        } else if(($value % 2) == 0) {
            return BLANK;
        } else if(($value % 2) == 1) {
            return GHOST;
        }
        return $value;
    }

    private function determineCssClass($value)
    {
        if($value == 0) {
            return "dgit-hidden";
        } else {
            return "dgit-face-".$value;
        }
    }
}