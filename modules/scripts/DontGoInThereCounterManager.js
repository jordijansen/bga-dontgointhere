/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereCounterManager.js
 * 
 * Script to manage counter elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/counter',
], (dojo, declare) => {
    return declare('dgit.counterManager', null, {
        constructor: function(game) {
            this.game = game;
            
            this.deckCounter = new ebg.counter();
            this.ghostTotalCounter = new ebg.counter();
            this.playerEndGameCurseCounters = [];
            this.playerSidePanelCurseCounters = [];
            this.currentPlayerSidePanelGhosts = new ebg.counter();
            this.playerDispeledCounters = [];
        },

        /**
         * Adjust deck counter by delta
         * @param {int} delta delta to adjust by
         */
        adjustDeckCounter: function (delta)
        { 
            this.deckCounter.incValue(delta);
        },
        
        /**
         * Adjust current player's ghost counter by delta
         * @param {int} delta delta to adjust by
         */
        adjustGhostCounter: function (delta)
        { 
            this.currentPlayerSidePanelGhosts.incValue(delta);
        },

        /**
         * Adjust ghosts rolled counter by delta
         * @param {int} delta delta to adjust by
         */
        adjustGhostTotalCounter: function (delta)
        {
            this.ghostTotalCounter.incValue(delta);
        },

        /**
         * Adjust player curse counters by delta
         * @param {Object} player 
         * @param {int} delta 
         */
        adjustPlayerCurses: function (player, delta)
        { 
            this.playerEndGameCurseCounters[player.id].incValue(delta);
            this.playerSidePanelCurseCounters[player.id].incValue(delta);
        },

        /**
         * Adjust player dispeled counter by delta
         * @param {Object} player 
         * @param {int} delta 
         */
        adjustPlayerDispeledCounter: function (player, delta)
        { 
            this.playerDispeledCounters[player.id].incValue(delta);
        },

        /**
         * Creates the deck counter
         * @param {int} deckSize Number of cards in the deck
         */
        createDeckCounter: function (deckSize)
        { 
            this.deckCounter.create('dgit_deck_counter');
            this.deckCounter.setValue(deckSize);
        },

        /**
         * Creates the counter for ghost total on rolled dice
         * @param {int} ghostTotal Number of ghosts rolled
         */
        createGhostTotalCounter: function (ghostTotal)
        {
            this.ghostTotalCounter.create('dgit_dice_total');
            this.ghostTotalCounter.setValue(ghostTotal);
        },

        /**
         * Creates the counters used to track a player's curses
         * @param {Object} player A player object
         */
        createPlayerCurseCounters: function (player)
        {
            this.playerEndGameCurseCounters[player.id] = new ebg.counter();
            this.playerEndGameCurseCounters[player.id].create('dgit_score_curse_counter_' + player.id);
            this.playerEndGameCurseCounters[player.id].setValue(player.curses);
            this.playerSidePanelCurseCounters[player.id] = new ebg.counter();
            this.playerSidePanelCurseCounters[player.id].create('dgit_player_' + player.id + '_side_panel_curse_counter');
            this.playerSidePanelCurseCounters[player.id].setValue(player.curses);
        },

        /**
         * Creates the counter used to track a player's dispeled cards
         * @param {Object} player A player object
         */
        createPlayerDispeledCounter: function (player)
        { 
            this.playerDispeledCounters[player.id] = new ebg.counter();
            this.playerDispeledCounters[player.id].create('dgit_player_' + player.id + '_dispeled_counter');
            this.playerDispeledCounters[player.id].setValue(player.cardsDispeled);
        },

        /**
         * Creates the counters used to track a player's ghost tokens
         * @param {Object} player A player object
         */
        createPlayerGhostCounters: function (player)
        {
            this.currentPlayerSidePanelGhosts.create('dgit_player_' + player.id + '_side_panel_ghost_counter');
            this.currentPlayerSidePanelGhosts.setValue(player.ghostTokens);
        },

        /**
         * Get value of deck counter
         * @returns {int} current value of deck counter
         */
        getDeckCounterValue: function ()
        {
            return this.deckCounter.getValue();
        },

        /**
         * Set the ghost total on rolled dice to a new value
         * @param {int} value To set ghost counter to
         */
        ghostTotalCounterToValue: function (value)
        { 
            this.ghostTotalCounter.toValue(value);
        },
    });
  });