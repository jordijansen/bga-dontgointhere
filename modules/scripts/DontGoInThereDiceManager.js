/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereDiceManager.js
 * 
 * Script to manage dice elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
], (dojo, declare) => {
    return declare('dgit.diceManager', ebg.core.gamegui, {
        constructor(game) {
            this.game = game;
        },

        /**
         * Setup dice info when view is loaded
         * @param {Object} gamedatas Array of game data
         */
        setup: function (gamedatas) {
            for (var dieKey in gamedatas.dice)
            {
                var die = gamedatas.dice[dieKey];
                dojo.addClass('dgit_die_'+die.id+'_face', die.cssClass);
            }
        },
    });
});