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
            this.playerCurseCounters = [];
            this.playerSidePanelCurseCounters = [];
            this.currentPlayerGhosts = new ebg.counter();
            this.currentPlayerSidePanelGhosts = new ebg.counter();
            this.playerDispeledCounters = [];
        },

        /**
         * Adjust current player's ghost counter by delta
         * @param {int} delta delta to adjust by
         */
        adjustGhostCounter: function (delta)
        { 
            this.currentPlayerGhosts.incValue(delta);
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
         * Creates the deck counter
         * @param {int} deckSize Number of cards in the deck
         */
        createDeckCounter: function (deckSize)
        { 
            debug('counterManager::createDeckCounter', deckSize);
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
            debug('counterManager::createPlayerCurseCounters', player);
            this.playerCurseCounters[player.id] = new ebg.counter();
            this.playerCurseCounters[player.id].create('dgit_player_' + player.id + '_curse_counter');
            this.playerCurseCounters[player.id].setValue(player.curses);
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
            debug('counterManager::createPlayerDispeledCounter', player);
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
            debug('counterManager::createPlayerGhostCounters', player);
            this.currentPlayerGhosts.create('dgit_player_' + player.id + '_ghost_counter');
            this.currentPlayerGhosts.setValue(player.ghostTokens);
            this.currentPlayerSidePanelGhosts.create('dgit_player_' + player.id + '_side_panel_ghost_counter');
            this.currentPlayerSidePanelGhosts.setValue(player.ghostTokens);
        },

        /**
         * Set the current players ghost counter to a new value
         * @param {int} value To set ghost counter to
         */
        ghostCounterToValue: function (value) { 
            this.currentPlayerGhosts.toValue(value);
            this.currentPlayerSidePanelGhosts.toValue(value);
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