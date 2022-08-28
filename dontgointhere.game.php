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
require_once("modules/DontGoInThereMeepleManager.class.php");
require_once("modules/DontGoInTherePlayerManager.class.php");
require_once("modules/DontGoInThereRoomManager.class.php");
class DontGoInThere extends Table
{
    function __construct()
    {
        parent::__construct();

        self::initGameStateLabels(array(
            CLOCKS_COLLECTED => 10,
            GHOSTS_ROLLED => 11,
            RESOLVED_ROOM_ABILITY => 12,
            ROOM_RESOLVER => 13,
            ROOM_RESOLVING => 14,
            SECRET_PASSAGE_REVEALED => 15,
            TOTAL_TURNS => 16,
            TURN_COUNTER => 17,
        ));

        $this->cardManager = new DontGoInThereCardManager($this);
        $this->diceManager = new DontGoInThereDiceManager($this);
        $this->meepleManager = new DontGoInThereMeepleManager($this);
        $this->playerManager = new DontGoInTherePlayerManager($this);
        $this->roomManager = new DontGoInThereRoomManager($this);
    }

    /**
     * Get name of game
     * @return string Name value of the game
     */
    protected function getGameName()
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
    protected function setupNewGame($players, $options = array())
    {
        // Setup players
        $this->playerManager->setupNewGame($players);
        // Setup meeples
        $this->meepleManager->setupNewGame($this->playerManager->getPlayers());
        // Setup dice
        $this->diceManager->setupNewGame();
        // Setup rooms
        $this->roomManager->setupNewGame();
        // Setup cards
        $this->cardManager->setupNewGame($this->playerManager->getPlayerCount(), $this->roomManager->getLibraryPosition());

        // Initialize global variables
        self::setGameStateInitialValue(CLOCKS_COLLECTED, DGIT_FALSE);
        self::setGameStateInitialValue(GHOSTS_ROLLED, -1);
        self::setGameStateInitialValue(RESOLVED_ROOM_ABILITY, DGIT_FALSE);
        self::setGameStateInitialValue(ROOM_RESOLVER, 0);
        self::setGameStateInitialValue(ROOM_RESOLVING, 0);
        self::setGameStateInitialValue(SECRET_PASSAGE_REVEALED, DGIT_FALSE);
        self::setGameStateInitialValue(TOTAL_TURNS, $this->playerManager->getPlayerCount() * 12);
        self::setGameStateInitialValue(TURN_COUNTER, 0);

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

        $roomCards = [];
        $roomMeeples = [];
        for ($roomNumber = 1; $roomNumber <= 3; $roomNumber++)
        {
            $roomCards[$roomNumber] = $this->cardManager->getUiData(ROOM_PREPEND . $roomNumber);
            $roomMeeples[$roomNumber] = $this->meepleManager->getUiData(ROOM_PREPEND . $roomNumber);
        }

        $data = [
            'constants' => get_defined_constants(true)['user'],
            'deckSize' => $this->cardManager->countCursedCards(DECK),
            'dice' => $this->diceManager->getUiData(),
            'faceupRooms' => $this->roomManager->getUiData(FACEUP),
            'facedownRooms' => $this->roomManager->getUiData(FACEDOWN),
            'meeplesInHand' => $this->meepleManager->getUiData(HAND),
            'meeplesInRooms' => $roomMeeples,
            'playerCards' => $this->cardManager->getUiData(HAND),
            'playerInfo' => $this->playerManager->getUiData($currentPlayerId),
            'roomCards' => $roomCards,
            'roomResolving' => $this->roomManager->getRoomResolving(),
            'secretPassageRevealed' => $this->roomManager->getSecretPassageRevealed(),
        ];
        return $data;
    }

    /**
     * Compute and return the current game progression.
     * @return int Percentage of game completed (between 0 and 100)
     */
    function getGameProgression()
    {
        return self::getGameStateValue(TURN_COUNTER) / self::getGameStateValue(TOTAL_TURNS) * 100;
    }


    /***********************************************************************************************
     *    UTILITY FUNCTIONS::Generic utility methods                                                *
     ************************************************************************************************/

    /**
     * Wrapper for getCurrentPlayerId
     * @return int ID of player who loaded screen
     */
    function getViewingPlayerId()
    {
        return self::getCurrentPlayerId();
    }

    /**
     * Increment turn counter for game progression calculation
     * @return void
     */
    function incrementTurnCounter()
    {
        $turnCounter = self::getGameStateValue(TURN_COUNTER);
        $turnCounter += 1;
        self::setGameStateValue(TURN_COUNTER, $turnCounter);
    }


    /***********************************************************************************************
     *    PLAYER ACTIONS::Methods when players trigger actions                                      *
     ************************************************************************************************/

    /**
     * Change the face of a die from BLANK to GHOST or vice versa
     * @param int $dieId Id of die to change
     * @return void
     */
    function changeDie($dieId)
    {
        $die = $this->diceManager->changeDieFace($dieId);
        $this->roomManager->setResolvedRoomAbility(DGIT_TRUE);

        self::notifyAllPlayers(
            CHANGE_DIE,
            clienttranslate('${player_name} changes a die to a ${face} face'),
            array(
                'player_name' => $this->getActivePlayerName(),
                'face' => $die->getFace() == BLANK ? clienttranslate('Blank') : clienttranslate('Ghost'),
                'die' => $die->getUiData(),
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Place a a meeple of the active player in the chosen room space
     * @param int $roomPosition UI position of target room
     * @param int $space Chosen space in the room
     * @return void
     */
    function placeMeeple($roomPosition, $space)
    {
        $player = $this->playerManager->getPlayer(self::getActivePlayerId());
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomPosition);
        $meeple = $this->meepleManager->moveMeepleToRoom($player, $roomPosition, $space);
        $meeplesInRoom = $this->meepleManager->getMeeples(ROOM_PREPEND . $room->getUiPosition());

        self::notifyAllPlayers(
            PLACE_MEEPLE,
            clienttranslate('${player_name} places a meeple in The ${roomName}'),
            array(
                'player_name' => $this->getActivePlayerName(),
                'roomName' => $room->getName(),
                'player' => $player->getUiData(),
                'room' => $room->getUiData(),
                'meeple' => $meeple->getUiData(),
            ));

        if ($room->getType() == SECRET_PASSAGE)
        {
            self::notifyAllPlayers(
                SECRET_PASSAGE_PEEK,
                clienttranslate('${player_name} can now see the hidden card in The Secret Passage'),
                array(
                    'player_name' => $this->getActivePlayerName(),
                )
            );
        }

        // If space 1 of attic player gains a ghost
        if ($room->getType() == ATTIC && $space == 1)
        {
            $this->playerManager->adjustPlayerGhosts($player->getId(), 1);
            self::notifyAllPlayers(
                ADJUST_GHOSTS,
                clienttranslate('${player_name} gains a Ghost token from placing a meeple in The Attic'),
                array(
                    'player_name' => $this->getActivePlayerName(),
                    'playerId' => $player->getId(),
                    'amount' => 1,
                )
            );
        }

        // If space 4 of nursery player discards a ghost
        if ($room->getType() == ATTIC && $space == 4 && $player->getGhostTokens() > 0)
        {
            $this->playerManager->adjustPlayerGhosts($player->getId(), -1);
            self::notifyAllPlayers(
                ADJUST_GHOSTS,
                clienttranslate('${player_name} discards a Ghost token from placing a meeple in The Nursery'),
                array(
                    'player_name' => $this->getActivePlayerName(),
                    'playerId' => $player->getId(),
                    'amount' => -1,
                )
            );
        }

        if (count($meeplesInRoom) == 3)
        {
            $this->roomManager->setRoomResolver(self::getActivePlayerId());
            $this->roomManager->setRoomResolving($room->getUiPosition());
            $this->gamestate->nextState(RESOLVE_ROOM);
        }
        else
        {
            self::incrementTurnCounter();
            $this->gamestate->nextState(NEXT_PLAYER);
        }
    }

    /**
     * Player inititated dice roll
     * @return void
     */
    function rollDice()
    {
        $roomResolving = $this->roomManager->getRoomResolving();
        $this->roomManager->setResolvedRoomAbility(DGIT_TRUE);

        self::notifyAllPlayers(
            'reroll',
            clienttranslate('${player_name} chooses to re-roll the dice'),
            array(
                'player_name' => self::getActivePlayerName(),
            )
        );

        $diceToRoll = $this->cardManager->countDiceIconsInRoom($roomResolving);
        $diceRolled = $this->diceManager->rollDice($diceToRoll);

        self::notifyAllPlayers(
            ROLL_DICE,
            clienttranslate('${player_name} rolls ${ghostsRolled} Ghost(s) on ${diceToRoll} dice'),
            array(
                'player_name' => self::getActivePlayerName(),
                'ghostsRolled' => $this->diceManager->getGhostsRolled(),
                'diceToRoll' => $diceToRoll,
                'diceRolled' => $diceRolled,
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Skip an optional action
     * @return void
     */
    function skip()
    {
        $roomUiPosition = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomUiPosition);
        $this->roomManager->setResolvedRoomAbility(DGIT_TRUE);

        self::notifyAllPlayers(
            SKIP,
            clienttranslate('${player_name} ${skipText}'),
            array(
                'player_name' => $this->getActivePlayerName(),
                'skipText' => $room->getAbilitySkipText(),
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Player takes a card from a room
     * @param int $cardId Id of card being taken
     * @return void
     */
    function takeCard($cardId)
    {
        $player = $this->playerManager->getPlayer();
        $room = $this->roomManager->getFaceupRoomByUiPosition($this->roomManager->getRoomResolving());
        $cardPosition = $this->cardManager->getCursedCardById($cardId)->getUiPosition();
        $card = $this->cardManager->takeCardFromRoom($cardId, $player);
        $this->playerManager->adjustPlayerCurses($player->getId(), $card->getCurses());
        $meeple = $this->meepleManager->triggerMeeple($player->getId(), $room->getUiPosition());

        self::notifyAllPlayers(
            TAKE_CARD,
            clienttranslate('${player_name} takes the ${cardName} and collects ${amount} curses'),
            array(
                'player_name' => self::getActivePlayerName(),
                'cardName' => $card->getName(),
                'amount' => $card->getCurses(),
                'player' => $player->getUiData(),
                'card' => $card->getUiData(),
                'meeple' => $meeple->getUiData(),
            )
        );

        if($room->getType() == LIBRARY) {
            if($cardPosition == 1) {
                $this->playerManager->adjustPlayerGhosts($player->getId(), 1);
                self::notifyAllPlayers(
                    ADJUST_GHOSTS,
                    clienttranslate('${player_name} gains a Ghost token from taking the first card in The Library'),
                    array(
                        'player_name' => $this->getActivePlayerName(),
                        'playerId' => $player->getId(),
                        'amount' => 1,
                    )
                );
            }
            if($cardPosition == 3 && $player->getGhostTokens() > 0) {
                $this->playerManager->adjustPlayerGhosts($player->getId(), -1);
                self::notifyAllPlayers(
                    ADJUST_GHOSTS,
                    clienttranslate('${player_name} discards a Ghost token from taking the third card in The Library'),
                    array(
                        'player_name' => $this->getActivePlayerName(),
                        'playerId' => $player->getId(),
                        'amount' => -1,
                    )
                );
            }
        }

        $this->gamestate->nextState(RESOLVE_ROOM);
    }


    /***********************************************************************************************
     *    GAME STATE ARGUMENTS::Methods to pass arguments required for a game state                 *
     ************************************************************************************************/

    /**
     * Args for room ability state
     * @return array Array of args
     */
    function argsRoomAbility()
    {
        $roomUiPosition = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomUiPosition);

        return array(
            'ability' => $room->getAbilityText(),
            'dice' => $this->diceManager->getUiData(),
            'room' => $room->getUiData(),
        );
    }

    /**
     * Args for select card state
     * @return array Array of args
     */
    function argsSelectCard()
    {
        $roomUiPosition = $this->roomManager->getRoomResolving();

        return array(
            'roomResolving' => $roomUiPosition,
        );
    }


    /***********************************************************************************************
     *    GAME STATE::Global game state actions                                                     *
     ************************************************************************************************/

    /**
     * Handle transition to next player
     * @return void
     */
    function stNextPlayer()
    {
        $nextPlayer = $this->activeNextPlayer();

        self::notifyAllPlayers(
            CHANGE_PLAYER,
            clienttranslate('${player_name} is now the active player'),
            array(
                'player_name' => self::getActivePlayerName(),
                'nextPlayer' => $nextPlayer,
            )
        );

        $this->gamestate->nextState(PLAYER_TURN);
    }

    /**
     * Handle transitions in resolve room phase
     * @return void
     */
    function stResolveRoom()
    {
        $roomResolving = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomResolving);

        // If room is Secret Passage reveal hidden card if it hasn't already been revealed
        if ($room->getType() == SECRET_PASSAGE && $this->roomManager->getSecretPassageRevealed() == DGIT_FALSE)
        {
            $this->roomManager->setSecretPassageRevealed(DGIT_TRUE);
            self::notifyAllPlayers(
                SECRET_PASSAGE_REVEAL,
                clienttranslate('All players can now see the hidden card in The Secret Passage'),
                array()
            );
        }

        // Roll dice if they haven't been rolled yet
        if ($this->diceManager->getGhostsRolled() < 0)
        {
            $diceToRoll = $this->cardManager->countDiceIconsInRoom($roomResolving);
            $diceRolled = $this->diceManager->rollDice($diceToRoll);

            self::notifyAllPlayers(
                ROLL_DICE,
                clienttranslate('${player_name} rolls ${ghostsRolled} Ghost(s) on ${diceToRoll} dice'),
                array(
                    'player_name' => self::getActivePlayerName(),
                    'ghostsRolled' => $this->diceManager->getGhostsRolled(),
                    'diceToRoll' => $diceToRoll,
                    'diceRolled' => $diceRolled,
                )
            );
        }

        // If room has a resolve ability we need to handle it
        if ($room->hasResolveAbility() && $this->roomManager->getResolvedRoomAbility() == DGIT_FALSE)
        {
            $this->gamestate->nextState(ROOM_RESOLUTION_ABILITY);
        }
        else
        {
            // Get next meeple in room
            $nextMeeple = $this->meepleManager->getTopMeepleInRoom($roomResolving);

            // Activate next meeple for card selection or move to next phase
            if ($nextMeeple)
            {
                $this->gamestate->changeActivePlayer($nextMeeple->getOwner());
                $this->gamestate->nextState(SELECT_CARD);
            }
            else
            {
                // Reset Dice
                $this->diceManager->resetDice();
                self::notifyAllPlayers(
                    RESET_DICE,
                    '',
                    array(
                        'dice' => $this->diceManager->getUiData(),
                    )
                );

                // Flip Room
                // Draw New Cards
                $this->gamestate->changeActivePlayer($this->roomManager->getRoomResolver());
                $this->gamestate->nextState(NEXT_PLAYER);
            }
        }
    }

    /**
     * Before a player selects a card they gain ghosts based on dice and flashlights
     * @return void
     */
    function stSelectCard()
    {
        $player = $this->playerManager->getPlayer();
        $roomResolving = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomResolving);
        $ghostsRolled = $this->diceManager->getGhostsRolled();
        $flashlights = $this->meepleManager->getMeepleFlashlights($player->getId(), $roomResolving);
        $ghostsGained = ($ghostsRolled - $flashlights) > 0 ? $ghostsRolled - $flashlights : 0;
        $this->playerManager->adjustPlayerGhosts($player->getId(), $ghostsGained);

        self::notifyAllPlayers(
            ADJUST_GHOSTS,
            clienttranslate('${player_name}\'s meeple gains ${amount} ghosts from The ${roomName}'),
            array(
                'player_name' => self::getActivePlayerName(),
                'amount' => $ghostsGained,
                'roomName' => $room->getName(),
                'playerId' => $player->getId(),
            )
        );
    }


    /***********************************************************************************************
    *    ZOMBIE::Functions to handle players in a zombie state                                     *
    ************************************************************************************************/

    /**
     * called each time it is the turn of a player who has quit the game (= "zombie" player)
     * @param object $state A game state object
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
