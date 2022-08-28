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
 * DontGoInTherePlayer.class.php
 * 
 * Player object
 */

class DontGoInTherePlayer extends APP_GameClass
{
    private $game;
    private $id;
    private $naturalOrder;
    private $name;
    private $avatar;
    private $color;
    private $curses;
    private $ghostTokens;
    private $cardsDispeled;
    private $eliminated = false;
    private $zombie = false;

    public function __construct($game, $row)
    {
        $this->game = $game;
        $this->id = (int) $row['id'];
        $this->naturalOrder = (int) $row['naturalOrder'];
        $this->name = $row['name'];
        $this->avatar = $row['avatar'];
        $this->color = $row['color'];
        $this->curses = $row['curses'] * -1;
        $this->ghostTokens = $row['ghostTokens'] * -1;
        $this->cardsDispeled = $row['cardsDispeled'];
        $this->eliminated = $row['eliminated'] == 1;
        $this->zombie = $row['zombie'] == 1;
    }

    public function getId(){ return $this->id; }
    public function getNaturalOrder(){ return $this->naturalOrder; }
    public function getName(){ return $this->name; }
    public function getAvatar(){ return $this->avatar; }
    public function getColor(){ return $this->color; }
    public function getCurses(){ return $this->curses; }
    public function getGhostTokens(){ return $this->ghostTokens; }
    public function getCardsDispeled(){ return $this->cardsDispeled; }
    public function isEliminated(){ return $this->eliminated; }
    public function isZombie(){ return $this->zombie; }

    /**
     * Get player uiData visible by current player
     * @param int $currentPlayerId Id of player who is viewing the page
     * @return array<mixed> An array of uiData for a player
     */
    public function getUiData($currentPlayerId = null)
    {
        return [
            'id' => $this->id,
            'naturalOrder' => $this->naturalOrder,
            'name' => $this->name,
            'color' => $this->color,
            'curses' => $this->curses,
            'ghostTokens' => ($this->id == $currentPlayerId) ? $this->ghostTokens : '?',
            'cardsDispeled' => $this->cardsDispeled,
        ];
    }
}