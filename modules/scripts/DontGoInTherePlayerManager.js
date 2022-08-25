/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInTherePlayerManager.js
 * 
 * Script to manage player elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
], (dojo, declare) => {
    return declare('dgit.playerManager', ebg.core.gamegui, {
        constructor: function(game) {
            this.game = game;
        },

        /**
         * Setup player info when view is loaded
         * @param {Object} gamedatas Array of game data
         */
        setup: function (gamedatas) {
            for(var playerKey in gamedatas.playerInfo)
            {
                var player = gamedatas.playerInfo[playerKey];

                // Place custom block in player panel
                this.game.util.placeBlock(PLAYER_SIDE_PANEL_TEMPLATE, 'player_board_' + player.id,
                    { player_id: player.id, player_natural_order: player.naturalOrder, player_color: player.color });

                // Unhide active player marker for first player
                if (player.id == this.game.getActivePlayerId())
                {
                    dojo.removeClass('dgit_player_' + player.id + '_active_player', 'dgit-hidden')
                }

                // Create player related counters
                this.game.counterManager.createPlayerCurseCounters(player);
                this.game.counterManager.createPlayerDispeledCounter(player);
                if (player.id == this.game.getCurrentPlayerId())
                {
                    this.game.counterManager.createPlayerGhostCounters(player);
                }

                // If player has dispeled cards, show dispeled card element
                if (player.cardsDispeled > 0) { 
                    dojo.removeClass('dgit_player_' + player.id + '_dispeled', 'dgit-hidden');
                }
            }
        },

        /**
         * Handle when a player gains or discards ghost tokens
         * @param {int} playerId Id of player gaining or discarding ghosts
         * @param {int} amount Delta of ghost tokens
         * @param {int} newTotal New total value of ghost tokens
         */
        adjustPlayerGhosts: function (playerId, amount, newTotal)
        { 
            // Animate gain ghosts
            if (amount > 0) {
                this.game.util.placeBlock(GHOST_TEMPLATE, 'dgit_ghost_tokens', { ghost_type: Math.floor(Math.random() * 24) + 1 });
                this.slideToObjectAndDestroy('dgit_moving_ghost', 'dgit_player_' + playerId + '_ghost_tracker');
            }

            // Animate discard ghosts
            if (amount < 0) {
                this.game.util.placeBlock(GHOST_TEMPLATE, 'dgit_player_' + playerId + '_ghost_tracker', { ghost_type: Math.floor(Math.random() * 24) + 1 });
                this.slideToObjectAndDestroy('dgit_moving_ghost', 'dgit_ghost_tokens');
            }

            // Adjust counter if current player
            if (playerId == this.game.getCurrentPlayerId()) {
                this.game.counterManager.ghostCounterToValue(newTotal);
            }
        },

        /**
         * Move the active player marker
         * @param {int} newActivePlayerId Id of new active player
         */
        changeActivePlayer: function (newActivePlayerId)
        { 
            dojo.query('.dgit-active-player').addClass('dgit-hidden')
            dojo.removeClass('dgit_player_' + newActivePlayerId + '_active_player', 'dgit-hidden');
        },
    });
});