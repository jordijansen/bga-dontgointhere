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
 * stats.inc.php
 *
 * DontGoInThere game statistics description
 *
 */

/*
    In this file, you are describing game statistics, that will be displayed at the end of the
    game.
    
    !! After modifying this file, you must use "Reload  statistics configuration" in BGA Studio backoffice
    ("Control Panel" / "Manage Game" / "Your Game")
    
    There are 2 types of statistics:
    _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
    _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

    Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean
    
    Once you defined your statistics there, you can start using "initStat", "setStat" and "incStat" method
    in your game logic, using statistics names defined below.
    
    !! It is not a good idea to modify this file when a game is running !!

    If your game is already public on BGA, please read the following before any change:
    http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
    Notes:
    * Statistic index is the reference used in setStat/incStat/initStat PHP method
    * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
    * Statistics IDs must be >=10
    * Two table statistics can't share the same ID, two player statistics can't share the same ID
    * A table statistic can have the same ID than a player statistics
    * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
    * Statistic name is the English description of the statistic as shown to players
    
*/

$stats_type = array(

    // Statistics global to table
    "table" => array(
        "turns_number" => array("id"=> 10,
                    "name" => totranslate("Number of turns"),
                    "type" => "int" ),
        "avg_curses" => array("id"=> 11,
                    "name" => totranslate("Average Curses"),
                    "type" => "float" ),
        "avg_ghosts" => array("id"=> 12,
                    "name" => totranslate("Average Ghosts"),
                    "type" => "float"),
        "avg_cards_dispeled" => array("id"=> 13,
                    "name" => totranslate("Average Cards Dispeled"),
                    "type" => "float"),
        "avg_curses_dispeled" => array("id" => 14,
                    "name" => totranslate("Average Curses Dispeled"),
                    "type" => "float"),
    ),
    
    // Statistics existing for each player
    "player" => array(
        "turns_number" => array("id"=> 10,
                    "name" => totranslate("Number of turns"),
                    "type" => "int" ),
        "curses_taken" => array("id"=> 11,
                    "name" => totranslate("Total Curses Taken"),
                    "type" => "int"),
        "curses_dispeled" => array("id"=> 12,
                    "name" => totranslate("Total Curses Dispeled"),
                    "type" => "int"),
        "ghosts_taken" => array("id"=> 13,
                    "name" => totranslate("Total Ghosts Taken"),
                    "type" => "int"),
        "ghosts_discarded" => array("id"=> 14,
                    "name" => totranslate("Total Ghosts Discarded"),
                    "type" => "int"),
        "amulets_taken" => array("id"=> 15,
                    "name" => totranslate("Amulets Taken"),
                    "type" => "int"),
        "cats_taken" => array("id"=> 16,
                    "name" => totranslate("Cats Taken"),
                    "type" => "int"),
        "clocks_taken" => array("id"=> 17,
                    "name" => totranslate("Clocks Taken"),
                    "type" => "int"),
        "dolls_taken" => array("id"=> 18,
                    "name" => totranslate("Dolls Taken"),
                    "type" => "int"),
        "holy_water_taken" => array("id"=> 19,
                    "name" => totranslate("Holy Water Taken"),
                    "type" => "int"),
        "masks_taken" => array("id"=> 20,
                    "name" => totranslate("Masks Taken"),
                    "type" => "int"),
        "mirrors_taken" => array("id"=> 21,
                    "name" => totranslate("Mirrors Taken"),
                    "type" => "int"),
        "music_boxes_taken" => array("id"=> 22,
                    "name" => totranslate("Music Boxes Taken"),
                    "type" => "int"),
        "portraits_taken" => array("id"=> 23,
                    "name" => totranslate("Portraits Taken"),
                    "type" => "int"),
        "rings_taken" => array("id"=> 24,
                    "name" => totranslate("Rings Taken"),
                    "type" => "int"),
        "tomes_taken" => array("id"=> 25,
                    "name" => totranslate("Tomes Taken"),
                    "type" => "int"),
        "twins_taken" => array("id"=> 26,
                    "name" => totranslate("Twins Taken"),
                    "type" => "int"),
        "amulets_dispeled" => array("id"=> 27,
                    "name" => totranslate("Amulets Dispeled"),
                    "type" => "int"),
        "cats_dispeled" => array("id"=> 28,
                    "name" => totranslate("Cats Dispeled"),
                    "type" => "int"),
        "clocks_dispeled" => array("id"=> 29,
                    "name" => totranslate("Clocks Dispeled"),
                    "type" => "int"),
        "dolls_dispeled" => array("id"=> 30,
                    "name" => totranslate("Dolls Dispeled"),
                    "type" => "int"),
        "holy_water_dispeled" => array("id"=> 31,
                    "name" => totranslate("Holy Water Dispeled"),
                    "type" => "int"),
        "masks_dispeled" => array("id"=> 32,
                    "name" => totranslate("Masks Dispeled"),
                    "type" => "int"),
        "mirrors_dispeled" => array("id"=> 33,
                    "name" => totranslate("Mirrors Dispeled"),
                    "type" => "int"),
        "music_boxes_dispeled" => array("id"=> 34,
                    "name" => totranslate("Music Boxes Dispeled"),
                    "type" => "int"),
        "portraits_dispeled" => array("id"=> 35,
                    "name" => totranslate("Portraits Dispeled"),
                    "type" => "int"),
        "rings_dispeled" => array("id"=> 36,
                    "name" => totranslate("Rings Dispeled"),
                    "type" => "int"),
        "tomes_dispeled" => array("id"=> 37,
                    "name" => totranslate("Tomes Dispeled"),
                    "type" => "int"),
        "twins_dispeled" => array("id"=> 38,
                    "name" => totranslate("Twins Dispeled"),
                    "type" => "int"),
        "amulets_curses" => array("id"=> 39,
                    "name" => totranslate("Curses from Amulets"),
                    "type" => "int"),
        "cats_curses" => array("id"=> 40,
                    "name" => totranslate("Curses from Cats"),
                    "type" => "int"),
        "clocks_curses" => array("id"=> 41,
                    "name" => totranslate("Curses from Clocks"),
                    "type" => "int"),
        "dolls_curses" => array("id"=> 42,
                    "name" => totranslate("Curses from Dolls"),
                    "type" => "int"),
        "holy_water_curses" => array("id"=> 43,
                    "name" => totranslate("Curses from Holy Water"),
                    "type" => "int"),
        "masks_curses" => array("id"=> 44,
                    "name" => totranslate("Curses from Masks"),
                    "type" => "int"),
        "mirrors_curses" => array("id"=> 45,
                    "name" => totranslate("Curses from Mirrors"),
                    "type" => "int"),
        "music_boxes_curses" => array("id"=> 46,
                    "name" => totranslate("Curses from Music Boxes"),
                    "type" => "int"),
        "portraits_curses" => array("id"=> 47,
                    "name" => totranslate("Curses from Portraits"),
                    "type" => "int"),
        "rings_curses" => array("id"=> 48,
                    "name" => totranslate("Curses from Rings"),
                    "type" => "int"),
        "tomes_curses" => array("id"=> 49,
                    "name" => totranslate("Curses from Tomes"),
                    "type" => "int"),
        "twins_curses" => array("id"=> 50,
                    "name" => totranslate("Curses from Twins"),
                    "type" => "int"),
    )

);
