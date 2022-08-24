/**
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
        constructor(game) {
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
    });
});