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
        constructor: function(game) {
            this.game = game;
        },

        /**
         * Setup dice info when view is loaded
         * @param {Object} gamedatas Array of game data
         */
        setup: function (gamedatas) {
            this.setDice(gamedatas.dice);
            this.game.counterManager.createGhostTotalCounter(this.getGhostTotal(gamedatas.dice));

            if (this.diceVisible(gamedatas.dice)) {
                dojo.removeClass('dgit_dice_total', 'dgit-hidden');
            }            
        },

        /**
         * Check if the dice should currently be visible on screen
         * @param {Object} dice Array of dice data
         * @returns {boolean} true = visible; false = not
         */
        diceVisible: function (dice)
        {
            for (var dieKey in dice) {
                var die = dice[dieKey];
                if (die.cssClass != 'dgit-hidden') {
                    return true;
                }
            }
            return false;
        },
         
        /**
         * Get total ghost faces on rolled dice
         * @param {Object} dice Array of dice data
         * @returns {int} Sum of ghost faces on dice
         */
        getGhostTotal: function (dice)
        {
            var ghostTotal = 0;
            for (var dieKey in dice) {
                var die = dice[dieKey];
                if (die.face == GHOST) {
                    ghostTotal++;
                }
            }
            return ghostTotal;
        },

        /**
         * Reset dice to an unrolled state
         * @param {Object} dice 
         */
        resetDice: function (dice)
        { 
            this.setDice(dice);
            for (dieValue = 1; dieValue <= 6; dieValue++) {
                dojo.query('.dgit-face-' + dieValue).removeClass('dgit-face-' + dieValue);
            }
            this.game.counterManager.ghostTotalCounterToValue(0);
            dojo.addClass('dgit_dice_total', 'dgit-hidden');
        },

        /**
         * When dice are rolled, handle all required UI changes
         * @param {Object} dice Array of dice data
         */
        rollDice: function (dice)
        { 
            this.setDice(dice);
            this.game.counterManager.ghostTotalCounterToValue(this.getGhostTotal(dice));
            dojo.removeClass('dgit_dice_total', 'dgit-hidden');
        },

        /**
         * Set dice elements to their correct faces
         * @param {Object} dice Array of dice data
         */
        setDice: function (dice)
        { 
            for (var dieKey in dice) {
                var die = dice[dieKey];
                dojo.removeClass('dgit_die_' + die.id, 'dgit-hidden');
                dojo.removeClass('dgit_die_' + die.id + '_face');
                dojo.addClass('dgit_die_' + die.id + '_face', 'dgit-die-face');
                dojo.addClass('dgit_die_' + die.id + '_face', die.cssClass);
                if (die.cssClass == 'dgit-hidden') {
                    dojo.addClass('dgit_die_' + die.id, die.cssClass);
                }
            }
        },
    });
});