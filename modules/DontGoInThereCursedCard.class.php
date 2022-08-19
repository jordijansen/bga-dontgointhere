<?php

/**
 * DontGoInThereCursedCard: Abstract object for a Cursed Card object
 */
abstract class DontGoInThereCursedCard extends APP_GameClass
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
    protected $curses = 0;
    protected $diceIcons = 0;
    protected $endGameTrigger = false;

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getCssClass() { return $this->cssClass; }
    public function getTooltipText() { return $this->tooltipText; }
    public function getCurses() { return $this->curses; }
    public function getDiceIcons() { return $this->diceIcons; }
    public function isEndGameTrigger() { return $this->endGameTrigger; }

    public function getUiData()
    {
        return[
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'cssClass' => $this->cssClass,
            'tooltipText' => $this->tooltipText,
            'curses' => $this->curses,
            'diceIcons' => $this->diceIcons,
            'endGameTrigger' => $this->endGameTrigger,
        ];
    }

    // Map of curse value to number of dice icons on a card
    protected static $cursesToDiceIcons = [
        1 => 2,
        2 => 2,
        3 => 1,
        4 => 1,
    ];

    /**
     * determineDiceIcons: Get the appropriate count of dice icons based on curse value of card
     */
    protected function determineDiceIcons($curses)
    {
        if(!isset(self::$cursesToDiceIcons[$curses]))
        {
            throw new BgaVisibleSystemException("getDiceIcons: Invalid curses amount $curses");
        }
        return self::$cursesToDiceIcons[$curses];
    }
}