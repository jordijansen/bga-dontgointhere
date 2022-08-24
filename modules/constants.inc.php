<?php

/**
 * Action names
 */
define('CHANGE_PLAYER', 'changePlayer');
define('PLACE_MEEPLE', 'placeMeeple');
define('SECRET_PASSAGE_PEEK', 'secretPassagePeek');

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

/**
 * JSTPL template names
 */
define('DECK_CARD_TEMPLATE', 'jstpl_deck_card');
define('MEEPLE_TEMPLATE', 'jstpl_meeple');
define('PLAYER_CARD_TEMPLATE', 'jstpl_player_card');
define('PLAYER_SIDE_PANEL_TEMPLATE', 'jstpl_player_side_panel');
define('ROOM_CARD_TEMPLATE', 'jstpl_room_card');

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
define('STATE_NEXT_PLAYER', 3);
define('STATE_GAME_END', 99);

/**
 * State names
 */
define('GAME_END', 'gameEnd');
define('GAME_SETUP', 'gameSetup');
define('NEXT_PLAYER', 'nextPlayer');
define('PLAYER_TURN', 'playerTurn');