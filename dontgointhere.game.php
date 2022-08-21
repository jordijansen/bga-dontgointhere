<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * dontgointhere.game.php
  *
  * The main file for the game logic.
  *
  */

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );
require_once("modules/constants.inc.php");
require_once("modules/DontGoInThereCardManager.class.php");
require_once("modules/DontGoInThereDiceManager.class.php");
require_once("modules/DontGoInTherePlayerManager.class.php");
require_once("modules/DontGoInThereRoomManager.class.php");
class DontGoInThere extends Table
{
	function __construct( )
	{
        parent::__construct();
        
        self::initGameStateLabels( array(
            CLOCKS_COLLECTED => 10,
        ) );

        $this->cardManager = new DontGoInThereCardManager($this);
        $this->diceManager = new DontGoInThereDiceManager($this);
        $this->playerManager = new DontGoInTherePlayerManager($this);
        $this->roomManager = new DontGoInThereRoomManager($this);
	}
	
    /**
     * Get name of game
     * @return string Name value of the game
     */
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "dontgointhere";
    }	

    /**
     * Initial setup of the game
     * @param array $players An array of players
     * @param array $options An array of game options
     * @return void
     */
    protected function setupNewGame( $players, $options = array() )
    {
        // Setup players
        $this->playerManager->setupNewGame($players);
        // Setup dice
        $this->diceManager->setupNewGame();
        // Setup rooms
        $this->roomManager->setupNewGame();
        // Setup cards
        $this->cardManager->setupNewGame($this->playerManager->getPlayerCount());

        // Initialize global variables
        self::setGameStateInitialValue(CLOCKS_COLLECTED, DGIT_FALSE);
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /**
     * Gather all informations about current game situation (visible by the current player)
     * @return array An array of game data
     */
    protected function getAllDatas()
    {
        $currentPlayerId = self::getCurrentPlayerId();
        $data = [
            'constants' => get_defined_constants(true)['user'],
            'deckSize' => $this->cardManager->countCursedCards('deck'),
            'dice' => $this->diceManager->getUiData(),
            'faceupRooms' => $this->roomManager->getUiData('faceup'),
            'facedownRooms' => $this->roomManager->getUiData('facedown'),
            'playerCards' => $this->cardManager->getUiData('hand'),
            'playerInfo' => $this->playerManager->getUiData($currentPlayerId),
            'roomCards' => [
                1 => $this->cardManager->getUiData('room_1'),
                2 => $this->cardManager->getUiData('room_2'),
                3 => $this->cardManager->getUiData('room_3'),
            ],
        ];
        return $data;
    }

    /**
     * Compute and return the current game progression.
     * @return int Percentage of game completed (between 0 and 100)
     */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


    /***********************************************************************************************
     *    UTILITY FUNCTIONS::Generic utility methods                                                *
     ************************************************************************************************/

    /**
     * Gives access to getCurrentPlayerId to other classes
     * @return int ID of player who loaded screen
     */
    function getViewingPlayerId()
    {
        return self::getCurrentPlayerId();
    }


    /***********************************************************************************************
    *    PLAYER ACTIONS::Methods when players trigger actions                                      *
    ************************************************************************************************/

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */

    
    /***********************************************************************************************
    *    GAME STATE ARGUMENTS::Methods to pass arguments required for a game state                 *
    ************************************************************************************************/

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */


    /***********************************************************************************************
    *    GAME STATE::Global game state actions                                                     *
    ************************************************************************************************/
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */


    /***********************************************************************************************
    *    ZOMBIE::Functions to handle players in a zombie state                                     *
    ************************************************************************************************/

    /**
     * called each time it is the turn of a player who has quit the game (= "zombie" player)
     * @param object $state A game state ovhect
     * @param int $active_player Database ID of a player
     * @throws feException 
     * @return void
     */
    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }

    
    /***********************************************************************************************
    *    DB UPGRADE::Functions to handle games with old DB schema                                  *
    ************************************************************************************************/

    /**
     * If database schema changes, apply changes to currently running games with old schema
     * @param int $from_version The version of the schema in the current game
     * @return void
     */
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//
    }    
}
