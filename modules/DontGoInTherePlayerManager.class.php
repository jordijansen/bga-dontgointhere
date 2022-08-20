<?php

require_once('DontGoInTherePlayer.class.php');

/**
 * Functions to manager players
 */
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
     * Returns a DontGoInTherePlayer object for active/specified player ID
     * @param int $playerId Database ID of a plyer
     * @return DontGoInTherePlayer A DontGoInTherePlayer object
     */
    public function getPlayer($playerId = null)
    {
        $playerId = $playerId ?? $this->game->getActivePlayerId();
        $players = $this->getPlayers([$playerId]);
        return $players[0];
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
}