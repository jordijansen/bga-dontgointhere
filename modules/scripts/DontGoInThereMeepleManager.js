/**
 * Script to manage meeple elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
], (dojo, declare) => {
    return declare('dgit.meepleManager', ebg.core.gamegui, {
        constructor(game) {
            this.game = game;
        },

        /**
         * Setup meeple info when view is loaded
         * @param {Object} gamedatas array of gamedata
         */
        setup: function (gamedatas) {
            // Create meeples in player hands
            for (var meeplesKey in gamedatas.meeplesInHand)
            {
                // Create meeple
                var meeple = gamedatas.meeplesInHand[meeplesKey];

                // Place meeple
                this.game.util.placeBlock(MEEPLE_TEMPLATE, 'dgit_player_' + meeple.owner + '_meeples',
                    { player_id: meeple.owner, meeple_id: meeple.id, meeple_css_class: meeple.cssClass });
            }
        },

        /**
         * Move a meeple from a player's hand to a space on a room
         * @param {Object} player Player object
         * @param {Object} meeple Meeple object
         * @param {Object} room Room object
         * @param {int} currentPlayerId The ID of the current player
         */
        moveMeepleToRoom: function (player, meeple, room)
        { 
            var meepleDiv = 'dgit_player_' + meeple.owner + '_meeple_' + meeple.id;
            var roomSpaceDiv = 'dgit_room_' + room.uiPosition + '_space_' + meeple.uiPosition;
            var roomHighlightDiv = 'dgit_room_' + room.uiPosition + '_space_highlight_' + meeple.uiPosition;
            this.attachToNewParent(meepleDiv, roomSpaceDiv);
            this.slideToObject(meepleDiv, roomSpaceDiv).play();
            dojo.setAttr(roomHighlightDiv, 'meeple', meeple.owner);

            if (room.type == SECRET_PASSAGE && player.id == this.game.getCurrentPlayerId()) {
                var secretPassageHiddenCard = dojo.query('div[special="secret-passage"')[0];
                if (secretPassageHiddenCard) {
                    dojo.removeClass(secretPassageHiddenCard.id, 'dgit-card-back');
                    dojo.removeClass(secretPassageHiddenCard.firstElementChild.id, 'dgit-hidden');
                }
                
            }
        },
    });
});