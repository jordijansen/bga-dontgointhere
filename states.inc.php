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
 * states.inc.php
 *
 * DontGoInThere game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

 
$machinestates = array(

    // The initial state. Please do not modify.
    STATE_GAME_SETUP => array(
        "name" => GAME_SETUP,
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => STATE_PLAYER_TURN )
    ),
    
    STATE_PLAYER_TURN => array(
    	"name" => PLAYER_TURN,
    	"description" => clienttranslate('${actplayer} must place a meeple into a room'),
    	"descriptionmyturn" => clienttranslate('${you} must place a meeple into a room'),
    	"type" => "activeplayer",
    	"possibleactions" => array( PLACE_MEEPLE ),
    	"transitions" => array( NEXT_PLAYER => STATE_NEXT_PLAYER, RESOLVE_ROOM => STATE_RESOLVE_ROOM )
    ),

    STATE_RESOLVE_ROOM => array(
        "name" => RESOLVE_ROOM,
        "type" => "game",
        "action" => "stResolveRoom",
        "transitions" => array( ROOM_RESOLUTION_ABILITY => STATE_ROOM_RESOLUTION_ABILITY, SELECT_CARD => STATE_SELECT_CARD, NEXT_PLAYER => STATE_NEXT_PLAYER )
    ),

    STATE_ROOM_RESOLUTION_ABILITY => array(
        "name" => ROOM_RESOLUTION_ABILITY,
        "description" => clienttranslate('${actplayer} ${ability}'),
        "descriptionmyturn" => clienttranslate('${you} ${ability}'),
        "type" => "activeplayer",
        "args" => "argsRoomAbility",
        "possibleactions" => array( CHANGE_DIE, ROLL_DICE, SKIP ),
        "transitions" => array( RESOLVE_ROOM => STATE_RESOLVE_ROOM )
    ),

    STATE_SELECT_CARD => array(
        "name" => SELECT_CARD,
        "description" => clienttranslate('${actplayer} must take a Cursed Card'),
        "descriptionmyturn" => clienttranslate('${you} must take a Cursed Card'),
        "type" => "activeplayer",
        "args" => "argsSelectCard",
        "action" => "stSelectCard",
        "possibleactions" => array( TAKE_CARD ),
        "transitions" => array( TRIGGER_CARD_EFFECT => STATE_TRIGGER_CARD_EFFECT )
    ),

    STATE_TRIGGER_CARD_EFFECT => array(
        "name" => TRIGGER_CARD_EFFECT,
        "description" => clienttranslate('${actplayer} ${ability}'),
        "descriptionmyturn" => clienttranslate('${you} ${ability}'),
        "type" => "activeplayer",
        "args" => "argsTriggerCardEffect",
        "action" => "stTriggerCardEffect",
        "possibleactions" => array(DISPEL_SET),
        "transitions" => array( RESOLVE_ROOM => STATE_RESOLVE_ROOM )
    ),

    STATE_NEXT_PLAYER => array(
        "name" => NEXT_PLAYER,
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,
        "transitions" => array( PLAYER_TURN => STATE_PLAYER_TURN, TRIGGER_GAME_END_CARD_EFFECTS => STATE_TRIGGER_GAME_END_CARD_EFFECTS )
    ),

    STATE_TRIGGER_GAME_END_CARD_EFFECTS => array(
        "name" => TRIGGER_GAME_END_CARD_EFFECTS,
        "type" => "game",
        "action" => "stTriggerGameEndCardEffects",
        "transitions" => array(GAME_END => STATE_GAME_END)
    ),
   
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    STATE_GAME_END => array(
        "name" => GAME_END,
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



