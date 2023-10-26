/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereMeepleManager.js
 * 
 * Script to manage meeple elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'dojo/on',
    'ebg/core/gamegui',
], (dojo, declare, on) => {
    return declare('dgit.meepleManager', ebg.core.gamegui, {
        constructor: function(game) {
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
                    { meeple_id: meeple.id, meeple_css_class: meeple.cssClass, meeple_type: meeple.type, meeple_owner: meeple.owner });
            }
        },

        /**
         * Move a meeple to a player's hand
         * @param {Object} meeple 
         */
        moveMeepleToHand: function (meeple) {
            var meepleDiv = 'dgit_meeple_' + meeple.id;
            var playerHandDiv = 'dgit_player_' + meeple.owner + '_meeples';
            this.game.attachToNewParent(meepleDiv, playerHandDiv);
            var moveMeeple = this.game.slideToObject(meepleDiv, playerHandDiv).play();

            on(moveMeeple, "End", function () {
                $(meepleDiv).style.removeProperty('top');
                $(meepleDiv).style.removeProperty('left');
            })
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
            var meepleDiv = 'dgit_meeple_' + meeple.id;
            var roomSpaceDiv = 'dgit_room_' + room.uiPosition + '_space_' + meeple.uiPosition;
            var roomHighlightDiv = 'dgit_room_' + room.uiPosition + '_space_highlight_' + meeple.uiPosition;
            this.game.attachToNewParent(meepleDiv, roomSpaceDiv);
            this.game.slideToObject(meepleDiv, roomSpaceDiv).play();
            dojo.setAttr(roomHighlightDiv, 'meeple', meeple.owner);
        },
    });
});