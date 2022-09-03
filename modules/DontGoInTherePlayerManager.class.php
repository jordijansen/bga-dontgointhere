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
 * DontGoInTherePlayerManager.class.php
 * 
 * Functions to manage players
 */

require_once('DontGoInTherePlayer.class.php');

class DontGoInTherePlayerManager extends APP_GameClass
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;
    }

    /**
     * Setup players for a new game
     * @param array $players An array of players
     * @return void
     */
    public function setupNewGame($players)
    {
        $gameInfos = $this->game->getGameinfos();
        $defaultColors = $gameInfos['player_colors'];

        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = [];
        foreach($players as $playerId => $player)
        {
            $color = array_shift($defaultColors);
            $values[] = "('".$playerId."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }

        $sql .= implode($values, ',');
        self::DbQuery($sql);
        $this->game->reattributeColorsBasedOnPreferences($players, $gameInfos['player_colors']);
        $this->game->reloadPlayersBasicInfos();
    }

    /**
     * Adjust player curses. Since low score wins we need to save the value as negative
     * @param int $playerId
     * @param int $amount
     * @return int
     */
    public function adjustPlayerCurses($playerId, $amount)
    {
        $player = self::getPlayer($playerId);
        $newCurseTotal = ($player->getCurses() + $amount) >= 0 ? $player->getCurses() + $amount : 0;
        self::DbQuery('UPDATE player SET player_score="' . $newCurseTotal * -1 . '" WHERE player_id="' . $playerId . '"');
        return $newCurseTotal;
    }

    /**
     * Adjust player dispeled value
     * @param int $playerId
     * @param mixed $amount
     * @return float|int
     */
    public function adjustPlayerDispeled($playerId, $amount)
    {
        $player = self::getPlayer($playerId);
        $newDispeledTotal = $player->getCardsDispeled() + $amount;
        self::DbQuery('UPDATE player SET player_cards_dispeled="' . $newDispeledTotal . '" WHERE player_id="' . $playerId . '"');
        return $newDispeledTotal;
    }

    /**
     * Adjust player ghosts. Since low value breaks we need to save the value as negative
     * @param mixed $playerId
     * @param mixed $amount
     * @return int
     */
    public function adjustPlayerGhosts($playerId, $amount)
    {
        $player = self::getPlayer($playerId);
        $newGhostTotal = ($player->getGhostTokens() + $amount) >= 0 ? $player->getGhostTokens() + $amount : 0;
        self::DbQuery('UPDATE player SET player_score_aux="' . $newGhostTotal * -1 . '" WHERE player_id="' . $playerId . '"');
        return $newGhostTotal;
    }

    /**
     * Returns a DontGoInTherePlayer object for active/specified player ID
     * @param int $playerId Database ID of a plyer
     * @return DontGoInTherePlayer A DontGoInTherePlayer object
     */
    public function getPlayer($playerId = null)
    {
        $playerId = $playerId ?? $this->game->getActivePlayerId();
        $players = $this->getPlayers([$playerId]);
        return array_shift($players);
    }

    /**
     * Get player name div with color
     * @param mixed $player
     * @return string
     */
    public function getPlayerNameColorDiv($player)
    {
        return '<span class="playername" style="#'.$player->getColor().';">'.$player->getName().'</span>';
    }

    /**
     * Get a player based on their natrual order
     * @param mixed $naturalOrder
     * @return DontGoInTherePlayer
     */
    public function getPlayerByNaturalOrder($naturalOrder)
    {
        $sql = "SELECT player_id id, player_no naturalOrder, player_name name, player_avatar avatar, player_color color, player_score curses, player_score_aux ghostTokens, player_cards_dispeled cardsDispeled, player_eliminated eliminated, player_zombie zombie FROM player WHERE player_no='".$naturalOrder."'";
        $rows = self::getObjectListFromDb($sql);
        return new DontGoInTherePlayer($this->game, array_shift($rows));
    }

    /**
     * Get the player to the right of a player
     * @param mixed $player
     * @return DontGoInTherePlayer
     */
    public function getPlayerOnRight($player) 
    {
        $playerCount = self::getPlayerCount();
        $playerNaturalOrder = $player->getNaturalOrder();
        $orderOfPlayerOnRight = ($playerNaturalOrder == 1) ? $playerCount : $playerNaturalOrder - 1;
        return self::getPlayerByNaturalOrder($orderOfPlayerOnRight);
    }

    /**
     * Returns an array of DontGoInTherePlayer objects for all/specified player IDs
     * @param array<int> $playerIds An array of player IDs from database
     * @return array<DontGoInTherePlayer> An array of DontGoInTherePlayer objects
     */
    public function getPlayers($playerIds = null)
    {
        $sql = "SELECT player_id id, player_no naturalOrder, player_name name, player_avatar avatar, player_color color, player_score curses, player_score_aux ghostTokens, player_cards_dispeled cardsDispeled, player_eliminated eliminated, player_zombie zombie FROM player";
        if(is_array($playerIds))
        {
            $sql .= " WHERE player_id IN ('".implode("','", $playerIds)."')";
        }
        $rows = self::getObjectListFromDb($sql);

        $players = [];
        foreach($rows as $row)
        {
            $player = new DontGoInTherePlayer($this->game, $row);
            $players[] = $player;
        }
        return $players;
    }

    /**
     * Get a list of all DontGoInTherePlayer objects, sorted by natural order, starting with the current player
     * @return array<DontGoInTherePlayer> an array of DontGoInTherePlayer objects
     */
    public function getPlayersInViewOrder()
    {
        $playerInfo = self::getPlayers();
        $playerCount = count($playerInfo);;
        $currentPlayer = self::findPlayerById($playerInfo, $this->game->getViewingPlayerId());

        if($currentPlayer) {
            $sortedPlayerInfo = [];
            $sortedPlayerInfo[] = $currentPlayer;
            $lastPlayerAdded = $currentPlayer;

            while(count($sortedPlayerInfo) < $playerCount)
            {
                $nextPlayerNaturalOrder = 0;
                if($lastPlayerAdded->getNaturalOrder() == $playerCount) {
                    $nextPlayerNaturalOrder = 1;
                } else {
                    $nextPlayerNaturalOrder = 1 + $lastPlayerAdded->getNaturalOrder();
                }
                $nextPlayer = self::findPlayerByNaturalOrder($playerInfo, $nextPlayerNaturalOrder);
                $sortedPlayerInfo[] = $nextPlayer;
                $lastPlayerAdded = $nextPlayer;
            }

            return $sortedPlayerInfo;
        } else {
            return $playerInfo;
        }
    }


    /**
     * Returns the number of players
     * @return int Number of players in the game
     */
    public function getPlayerCount()
    {
        return intval(self::getUniqueValueFromDB("SELECT COUNT(*) FROM player"));
    }


    /**
     * Get all ui data visible by player id
     * @param int $playerId Database ID of a player
     * @return array<mixed> Array of uiData for a player
     */
    public function getUiData($playerId)
    {
        $uiData = [];
        foreach($this->getPlayers() as $player)
        {
            $uiData[] = $player->getUiData($playerId);
        }

        return $uiData;
    }

    /**
     * Return a DontGoInTherePlayer of specified ID from a list of players
     * @param array<DontGoInTherePlayer> $players An array of DontGoInTherePlayer objects
     * @param int $playerId A player ID
     * @return mixed a DontGoInTherePlayer object if it exists in the list, otherwise false
     */
    private function findPlayerById($players, $playerId)
    {
        foreach($players as $player)
        {
            if($playerId == $player->getId())
            {
                return $player;
            }
        }

        return false;
    }

    /**
     * Return a DontGoInTherePlayer of specified natural order from a list of players
     * @param array<DontGoInTherePlayer> $players An array of DontGoInTherePlayer objects
     * @param int $playerId A player's natural order
     * @return mixed a DontGoInTherePlayer object if it exists in the list, otherwise false
     */
    private function findPlayerByNaturalOrder($players, $playerNaturalOrder)
    {
        foreach($players as $player)
        {
            if($playerNaturalOrder == $player->getNaturalOrder())
            {
                return $player;
            }
        }

        return false;
    }

    /**
     * The player(s) with the most ghosts gain curses
     * @return void
     */
    public function handleGameEndGhosts()
    {
        $players = self::getPlayers();
        $playersWithMostGhosts = [];
        $mostGhosts = 0;

        // Get player(s) with the most ghosts
        foreach($players as $player)
        {
            $this->game->notifyAllPlayers(
                REVEAL_PLAYER_ROW,
                '',
                array(
                    'player' => $player->getUiData(),
                    'ghosts' => $player->getGhostTokens(),
                )
            );
            if($player->getGhostTokens() > $mostGhosts) {
                $playersWithMostGhosts = [];
                $playersWithMostGhosts[] = $player;
                $mostGhosts = $player->getGhostTokens();
            } else if($player->getGhostTokens() > 0 && $player->getGhostTokens() == $mostGhosts) {
                $playersWithMostGhosts[] = $player;
            }
        }

        // Those players get curses equal to half the ghosts
        foreach($playersWithMostGhosts as $player) 
        {
            $cursesToGain = floor($mostGhosts / 2);
            self::adjustPlayerCurses($player->getId(), $cursesToGain);

            $this->game->notifyAllPlayers(    
                GAIN_CURSES,    
                clienttranslate('${player_name} gains ${amount} ${plural} from having the most ghosts'),
                array(
                    'player_name' => self::getPlayerNameColorDiv($player),
                    'amount' => $cursesToGain,
                    'plural' => $cursesToGain == 1 ? clienttranslate('Curse') : clienttranslate('Curses'),
                    'player' => $player->getUiData(),
                )
            );
        }
    }
}