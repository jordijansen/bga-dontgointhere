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
 * gameoptions.inc.php
 *
 * DontGoInThere game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in dontgointhere.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

require_once(__DIR__.'/modules/constants.inc.php');


$cursed_card_options = [
    RANDOM_CURSED_CARD => [
        'default' => true,
        'name' => totranslate('Random')
    ],
    AMULET => [
        'name' => totranslate('Amulet'),
    ],
    CAT => [
        'name' => totranslate('Cat'),
    ],
    CLOCK => [
        'name' => totranslate('Clock'),
    ],
    DOLL => [
        'name' => totranslate('Doll'),
    ],
    HOLY_WATER => [
        'name' => totranslate('Holy Water'),
    ],
    MASK => [
        'name' => totranslate('Mask'),
    ],
    MIRROR => [
        'name' => totranslate('Mirror'),
    ],
    MUSIC_BOX => [
        'name' => totranslate('Music Box'),
    ],
    PORTRAIT => [
        'name' => totranslate('Portrait'),
    ],
    RING => [
        'name' => totranslate('Ring'),
    ],
    TOME => [
        'name' => totranslate('Tome'),
    ],
    TWIN => [
        'name' => totranslate('Twin'),
    ]
];

$game_options = [

    CURSED_CARDS_OPTION_ID => [
        'name' => totranslate('Cursed Cards'),
        'values' => [
            CURSED_CARDS_OPTION_STANDARD => [
                'default' => true,
                'name' => totranslate('Standard'),
                'description' => totranslate('Random cursed cards are used'),
            ],
            CURSED_CARDS_OPTION_CUSTOM => [
                'name' => totranslate('Custom'),
                'description' => totranslate('Select the cursed cards to use (or random). Note: if you choose duplicate card types, random ones will be picked instead.'),
            ]
        ]
    ],
    CURSED_CARDS_1_ID => [
        'name' => totranslate('Cursed Card #1'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
        ],
    ],
    CURSED_CARDS_2_ID => [
        'name' => totranslate('Cursed Card #2'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
        ]
    ],
    CURSED_CARDS_3_ID => [
        'name' => totranslate('Cursed Card #3'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
        ],
    ],
    CURSED_CARDS_4_ID => [
        'name' => totranslate('Cursed Card #4'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
        ],
    ],
    CURSED_CARDS_5_ID => [
        'name' => totranslate('Cursed Card #5'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
        ],
    ],
    CURSED_CARDS_6_ID => [
        'name' => totranslate('Cursed Card #6'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
            [
                'type' => 'minplayers',
                'value' => [3,4,5]
            ]
        ],
    ],
    CURSED_CARDS_7_ID => [
        'name' => totranslate('Cursed Card, #7'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
            [
                'type' => 'minplayers',
                'value' => [4,5]
            ]
        ],
    ],
    CURSED_CARDS_8_ID => [
        'name' => totranslate('Cursed Card #8'),
        'values' => $cursed_card_options,
        'displaycondition' => [
            [
                'type' => 'otheroption',
                'id' => CURSED_CARDS_OPTION_ID,
                'value' => [CURSED_CARDS_OPTION_CUSTOM]
            ],
            [
                'type' => 'minplayers',
                'value' => [5]
            ]
        ],
    ],
];


