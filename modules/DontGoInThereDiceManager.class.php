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
 * DontGoInThereDiceManager.class.php
 * 
 * Functions to manage dice
 */

require_once('DontGoInThereDie.class.php');

class DontGoInThereDiceManager extends APP_GameClass
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;
    }

    /**
     * Setup dice for a new game
     * @return void
     */
    public function setupNewGame()
    {
        // Create six dice
        $sql = "INSERT INTO die (die_id) VALUES ";
        $values = [];
        for($dieNumber = 1; $dieNumber <= 6; $dieNumber++)
        {
            $values[] = "('".$dieNumber."')";
        }

        $sql .= implode($values, ',');
        self::DbQuery($sql);
    }

    /**
     * Change the face of a die from BLANK to GHOST or vice versa
     * @param int $dieId Id of die to changge
     * @return DontGoInThereDie Updated die object
     */
    public function changeDieFace($dieId)
    {
        $die = self::getDie($dieId);
        $newValue = 0;
        $ghostTotal = self::getGhostsRolled();

        // Change from blank to ghost
        if($die->getFace() == BLANK) {
            $newValue = $die->getValue() - 1;
            self::setGhostsRolled($ghostTotal + 1);
        }
        // Change from ghost to blank
        if($die->getFace() == GHOST ){
            $newValue = $die->getValue() + 1;
            self::setGhostsRolled($ghostTotal - 1);
        }

        self::DbQuery("UPDATE die SET die_value ='".$newValue."' WHERE die_id='".$dieId."'");
        return self::getDie($dieId);
    }

    /**
     * Returns a DontGoInThereDie object for the specified die ID
     * @param int $dieId Database ID of a die
     * @return DontGoInThereDie A DontGoInThereDie object
     */
    public function getDie($dieId)
    {
        $dice = $this->getDice([$dieId]);
        return $dice[0];
    }

    /**
     * Returns an array of DontGoInThereDie objects for all/specified die IDs
     * @param array<int> $dieIds An array of die IDs from database
     * @return array<DontGoInThereDie> An array of DontGoInThereDie objects
     */
    public function getDice($dieIds = null)
    {
        $sql = "SELECT die_id id, die_value value FROM die";
        if(is_array($dieIds))
        {
            $sql .= " WHERE die_id IN ('".implode("','", $dieIds)."')";
        }
        $rows = self::getObjectListFromDb($sql);

        $dice = [];
        foreach($rows as $row)
        {
            $die = new DontGoInThereDie($this->game, $row);
            $dice[] = $die;
        }
        return $dice;
    }

    /**
     * Get the game state value for ghosts rolled
     * @return int Number of ghosts rolled on dice
     */
    public function getGhostsRolled()
    {
        return $this->game->getGameStateValue(GHOSTS_ROLLED);
    }

    /**
     * Get visible dice ui data for all/specified dieIds
     * @param array<int> $dieIds An array of die IDs from database
     * @return array<mixed> Array of uiData for a die
     */
    public function getUiData($dieIds = null)
    {
        $uiData = [];
        foreach($this->getDice($dieIds) as $die)
        {
            $uiData[] = $die->getUiData();
        }

        return $uiData;
    }

    /**
     * Reset all dice to an unrolled value
     * @return void
     */
    public function resetDice()
    {
        self::DbQuery("UPDATE die SET die_value='0'");
        self::setGhostsRolled(-1);
    }

    /**
     * Roll all dice required for resolving a room
     * @param int $numberOfDice How many dice need to be rolled
     * @return array<array> Ui data of the rolled dice
     */
    public function rollDice($numberOfDice)
    {
        $ghostsRolled = 0;
        $dice = [];

        for($dieNumber = 1; $dieNumber <= $numberOfDice; $dieNumber++)
        {
            self::rollDie($dieNumber);
            $die = self::getDie($dieNumber);
            $dice[] = $die->getUiData();

            if($die->getFace() == GHOST) {
                $ghostsRolled++;
            }
        }

        self::setGhostsRolled($ghostsRolled);
        return $dice;
    }

    /**
     * Roll a single D6 and persist value in database
     * @param int $dieId Id of die being rolled
     * @return void
     */
    private function rollDie($dieId) 
    {
        $rolledValue = rand(1, 6);
        self::DbQuery("UPDATE die SET die_value='" . $rolledValue . "' WHERE die_id='" . $dieId . "'");
    }

    /**
     * Set the game state value for ghosts rolled
     * @param int $value Number of ghosts rolled on dice
     * @return void
     */
    public function setGhostsRolled($value)
    {
        $this->game->setGameStateValue(GHOSTS_ROLLED, $value);
    }
}