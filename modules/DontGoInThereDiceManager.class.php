<?php

require_once('DontGoInThereDie.class.php');

/**
 * Functions to manage dice
 */
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
}