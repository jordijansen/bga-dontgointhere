/**
 * Script to manage player elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
    g_gamethemeurl + 'modules/scripts/DontGoInThereCounterManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInThereUtilities.js',
], (dojo, declare) => {
    return declare('dgit.playerManager', ebg.core.gamegui, {
        constructor() {
            this.counterManager = new dgit.counterManager();
            this.util = new dgit.utilities();
        },

        /**
         * Setup player info when view is loaded
         * @param {Object} gamedatas Array of game data
         * @param {int} activePlayerId Id of active player
         * @param {int} currentPlayerId Id of current player
         */
        setup: function (gamedatas, activePlayerId, currentPlayerId) {
            for(var playerKey in gamedatas.playerInfo)
            {
                var player = gamedatas.playerInfo[playerKey];

                // Place custom block in player panel
                this.util.placeBlock(PLAYER_SIDE_PANEL_TEMPLATE, 'player_board_' + player.id,
                    { player_id: player.id, player_natural_order: player.naturalOrder, player_color: player.color });

                // Unhide active player marker for first player
                if (player.id == activePlayerId)
                {
                    dojo.removeClass('dgit_player_' + player.id + '_active_player', 'dgit-hidden')
                }

                // Create player related counters
                this.counterManager.createPlayerCurseCounters(player);
                this.counterManager.createPlayerDispeledCounter(player);
                if (player.id == currentPlayerId)
                {
                    this.counterManager.createPlayerGhostCounters(player);
                }

                // If player has dispeled cards, show dispeled card element
                if (player.cardsDispeled > 0) { 
                    dojo.removeClass('dgit_player_' + player.id + '_dispeled', 'dgit-hidden');
                }
            }
        },
    });
});