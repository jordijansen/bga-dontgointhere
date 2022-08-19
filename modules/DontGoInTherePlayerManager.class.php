<?php

require_once('DontGoInTherePlayer.class.php');

/**
 * DontGoInTherePlayerManager: functions to manager players
 */
class DontGoInTherePlayerManager extends APP_GameClass
{
    public $game;

    public function __construct($game)
    {
        $this->game = $game;
    }

    /**
     * setupNewGame: Setup players for a new game
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
     * getPlayer: Returns a DontGoInTherePlayer object for active/specified player ID
     */
    public function getPlayer($playerId = null)
    {
        $playerId = $playerId ?? $this->game->getActivePlayerId();
        $players = $this->getPlayers([$playerId]);
        return $players[0];
    }

    /**
     * getPlayers: Returns an array of DontGoInTherePlayer objects for all/specified player IDs
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
     * getPlayerCount: Returns the number of players
     */
    public function getPlayerCount()
    {
        return intval(self::getUniqueValueFromDB("SELECT COUNT(*) FROM player"));
    }

    /**
     * getUiData: Get all ui data visible by player id
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