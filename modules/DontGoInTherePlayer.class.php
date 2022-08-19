<?php

/**
 * DontGoInTherePlayer: Player object
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
        $this->curses = $row['curses'];
        $this->ghostTokens = $row['ghostTokens'];
        $this->cardsDispeled = $row['cardsDispeled'];
        $this->eliminated = $row['eliminated'] == 1;
        $this->zombie = $row['zombie'] == 1;
    }

    public function getId(){ return $this->id; }
    public function getNaturalOrder(){ return $this->naturalOrder; }
    public function getName(){ return $this->name; }
    public function getAvatar(){ return $this->avatar; }
    public function getColor(){ return $this->color; }
    public function getCurses(){ return $this->sccursesore; }
    public function getGhostTokens(){ return $this->ghostTokens; }
    public function getCardsDispeled(){ return $this->cardsDispeled; }
    public function isEliminated(){ return $this->eliminated; }
    public function isZombie(){ return $this->zombie; }

    /**
     * getUiData: Get player ui data visible by current player
     */
    public function getUiData($currentPlayerId = null)
    {
        return[
            'id' => $this->id,
            'naturalOrder' => $this->naturalOrder,
            'name' => $this->name,
            'curses' => $this->curses,
            'ghostTokens' => ($this->id == $currentPlayerId) ? $this->ghostTokens : '?',
            'cardsDispeled' => $this->cardsDispeled,
        ];
    }
}