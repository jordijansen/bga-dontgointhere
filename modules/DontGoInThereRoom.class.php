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
 * DontGoInThereRoom.class.php
 * 
 * Abstract room object
 */

abstract class DontGoInThereRoom extends APP_GameClass
{
    protected $game;

    public function __construct($game)
    {
        $this->game = $game;
    }

    protected $id = 0;
    protected $name = '';
    protected $type = 0;
    protected $cssClass = '';
    protected $tooltipText = '';
    protected $flipSideRoom = 0;
    protected $uiPosition = 0;

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getCssClass() { return $this->cssClass; }
    public function getTooltipText() { return $this->tooltipText; }
    public function getFlipSideRoom() { return $this->flipSideRoom; }
    public function getUiPosition() { return $this->uiPosition; }

    /**
     * Get room uiData visible by current player
     * @return array of uiData for a room
     */
    public function getUiData()
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'cssClass' => $this->cssClass,
            'tooltipText' => $this->tooltipText,
            'flipSideRoom' => $this->flipSideRoom,
            'uiPosition' => $this->uiPosition,
        ];
    }
}