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
 * material.inc.php
 *
 * DontGoInThere game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/


$this->CURSE_TYPE_LABEL = [
    0 => totranslate('Amulet'),
    1 => totranslate('Cat'),
    2 => totranslate('Clock'),
    3 => totranslate('Doll'),
    4 => totranslate('Holy Water'),
    5 => totranslate('Mask'),
    6 => totranslate('Mirror'),
    7 => totranslate('Music Box'),
    8 => totranslate('Portrait'),
    9 => totranslate('Ring'),
    10  => totranslate('Tome'),
    11 => totranslate('Twin')
];
