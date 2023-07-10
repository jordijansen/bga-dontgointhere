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
 * constants.inc.php
 * 
 * Constant defintion file
 */

/**
 * Action names
 */
define('ADJUST_GHOSTS', 'adjustGhosts');
define('CHANGE_DIE', 'changeDie');
define('CHANGE_PLAYER', 'changePlayer');
define('DISPEL_CARDS', 'dispelCards');
define('DISPEL_SET', 'dispelSet');
define('FLIP_ROOM', 'flipRoom');
define('FLIP_ROOM_FACEDOWN', 'flipRoomFacedown');
define('GAIN_CURSES', 'gainCurses');
define('NEW_CARDS', 'newCards');
define('PLACE_MEEPLE', 'placeMeeple');
define('RESET_DICE', 'resetDice');
define('RETURN_MEEPLE', 'returnMeeple');
define('REVEAL_PLAYER_ROW', 'revealPlayerRow');
define('REVEAL_WINNERS', 'revealWinners');
define('ROLL_DICE', 'rollDice');
define('SECRET_PASSAGE_PEEK', 'secretPassagePeek');
define('SECRET_PASSAGE_REVEAL', 'secretPassageReveal');
define('SKIP', 'skip');
define('TAKE_CARD', 'takeCard');
define('TRIGGER_MASK', 'triggerMask');

/**
 * Card types
 */
define('AMULET', 0);
define('CAT', 1);
define('CLOCK', 2);
define('DOLL', 3);
define('HOLY_WATER', 4);
define('MASK', 5);
define('MIRROR', 6);
define('MUSIC_BOX', 7);
define('PORTRAIT', 8);
define('RING', 9);
define('TOME', 10);
define('TWIN', 11);

/**
 * Component locations
 */
define('DECK', 'deck');
define('DISPELED', 'dispeled');
define('FACEDOWN', 'facedown');
define('FACEUP', 'faceup');
define('HAND', 'hand');
define('HOLDING', 'holding');
define('ROOM_PREPEND', 'room_');
define('TRASH', 'trash');

/**
 * Database columns
 */
define('ID', 'id');
define('LOCATION', 'location');
define('LOCATION_ARG', 'location_arg');
define('TYPE', 'type');
define('TYPE_ARG', 'type_arg');

/**
 * Die faces
 */
define('HIDDEN', 0);
define('BLANK', 1);
define('GHOST', 2);

/**
 * Generic
 */
define('DGIT_FALSE', 0);
define('DGIT_TRUE', 1);

/**
 * Global variables
 */
define('CLOCKS_COLLECTED', 'clocks_collected');
define('GHOSTS_ROLLED', 'ghosts_rolled');
define('LAST_SELECTED_CARD', 'last_selected_card');
define('RESOLVED_ROOM_ABILITY', 'resolved_room_ability');
define('RESOLVED_ROOM_GHOSTS', 'resolved_room_ghosts');
define('ROOM_RESOLVER', 'room_resolver');
define('ROOM_RESOLVING', 'room_resolving');
define('SECRET_PASSAGE_REVEALED', 'secret_passage_revealed');
define('TOTAL_TURNS', 'total_turns');
define('TURN_COUNTER', 'turn_counter');

/**
 * JSTPL template names
 */
define('CURSED_CARD_TEMPLATE', 'jstpl_cursed_card');
define('DECK_CARD_TEMPLATE', 'jstpl_deck_card');
define('GHOST_TEMPLATE', 'jstpl_ghost_token');
define('MEEPLE_TEMPLATE', 'jstpl_meeple');
define('PLAYER_SIDE_PANEL_TEMPLATE', 'jstpl_player_side_panel');

/**
 * Meeple types
 */
define('PURPLE', 0);
define('RED', 1);
define('TEAL', 2);
define('WHITE', 3);
define('YELLOW', 4);

/**
 * Room types
 */
define('ATTIC', 0);
define('BASEMENT', 1);
define('HALLWAY', 2);
define('LIBRARY', 3);
define('NURSERY', 4);
define('SECRET_PASSAGE', 5);

/**
 * State IDS
 */
define('STATE_GAME_SETUP', 1);
define('STATE_PLAYER_TURN', 2);
define('STATE_RESOLVE_ROOM', 10);
define('STATE_ROOM_RESOLUTION_ABILITY', 11);
define('STATE_SELECT_CARD', 12);
define('STATE_TRIGGER_CARD_EFFECT', 13);
define('STATE_NEXT_PLAYER', 20);
define('STATE_TRIGGER_GAME_END_CARD_EFFECTS', 30);
define('STATE_GAME_END_CHECK_GHOSTS', 31);
define('STATE_GAME_END', 99);

/**
 * State names
 */
define('GAME_END', 'gameEnd');
define('GAME_END_CHECK_GHOSTS', 'gameEndCheckGhosts');
define('GAME_SETUP', 'gameSetup');
define('NEXT_PLAYER', 'nextPlayer');
define('PLAYER_TURN', 'playerTurn');
define('RESOLVE_ROOM', 'resolveRoom');
define('ROOM_RESOLUTION_ABILITY', 'roomResolutionAbility');
define('SELECT_CARD', 'selectCard');
define('TRIGGER_CARD_EFFECT', 'triggerCardEffect');
define('TRIGGER_GAME_END_CARD_EFFECTS', 'triggerGameEndCardEffects');

/**
 * Game Options
 */
const CURSED_CARDS_OPTION_ID = 100;
const CURSED_CARDS_OPTION = 'CURSED_CARDS_OPTION';

const CURSED_CARDS_OPTION_STANDARD = 1;
const CURSED_CARDS_OPTION_CUSTOM = 2;

const CURSED_CARDS_1_ID = 101;
const CURSED_CARDS_1 = 'CURSED_CARDS_1';
const CURSED_CARDS_2_ID = 102;
const CURSED_CARDS_2 = 'CURSED_CARDS_2';
const CURSED_CARDS_3_ID = 103;
const CURSED_CARDS_3 = 'CURSED_CARDS_3';
const CURSED_CARDS_4_ID = 104;
const CURSED_CARDS_4 = 'CURSED_CARDS_4';
const CURSED_CARDS_5_ID = 105;
const CURSED_CARDS_5 = 'CURSED_CARDS_5';
const CURSED_CARDS_6_ID = 106;
const CURSED_CARDS_6 = 'CURSED_CARDS_6';
const CURSED_CARDS_7_ID = 107;
const CURSED_CARDS_7 = 'CURSED_CARDS_7';
const CURSED_CARDS_8_ID = 108;
const CURSED_CARDS_8 = 'CURSED_CARDS_8';

const RANDOM_CURSED_CARD = 99;


