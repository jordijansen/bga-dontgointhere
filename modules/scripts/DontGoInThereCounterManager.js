/**
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
        constructor(game) {
            this.game = game;
            
            this.deckCounter = new ebg.counter();
            this.playerCurseCounters = [];
            this.playerSidePanelCurseCounters = [];
            this.currentPlayerGhosts = new ebg.counter();
            this.currentPlayerSidePanelGhosts = new ebg.counter();
            this.playerDispeledCounters = [];
        },

        /**
         * Creates the deck counter
         * @param {int} deckSize Number of cards in the deck
         */
        createDeckCounter(deckSize)
        { 
            debug('counterManager::createDeckCounter', deckSize);
            this.deckCounter.create('dgit_deck_counter');
            this.deckCounter.setValue(deckSize);
        },

        /**
         * Creates the counters used to track a player's curses
         * @param {Object} player A player object
         */
        createPlayerCurseCounters(player)
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
        createPlayerDispeledCounter(player)
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
        createPlayerGhostCounters(player)
        { 
            debug('counterManager::createPlayerGhostCounters', player);
            this.currentPlayerGhosts.create('dgit_player_' + player.id + '_ghost_counter');
            this.currentPlayerGhosts.setValue(player.ghostTokens);
            this.currentPlayerSidePanelGhosts.create('dgit_player_' + player.id + '_side_panel_ghost_counter');
            this.currentPlayerSidePanelGhosts.setValue(player.ghostTokens);
        },
    });
  });