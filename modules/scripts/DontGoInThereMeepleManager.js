/**
 * Script to manage meeple elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
    g_gamethemeurl + 'modules/scripts/DontGoInThereUtilities.js',
], (dojo, declare) => {
    return declare('dgit.meepleManager', ebg.core.gamegui, {
        constructor() { 
            this.util = new dgit.utilities();
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
                this.util.placeBlock(MEEPLE_TEMPLATE, 'dgit_player_' + meeple.owner + '_meeples',
                    { player_id: meeple.owner, meeple_id: meeple.id, meeple_css_class: meeple.cssClass });
            }
        },

        /**
         * Move a meeple from a players hand to a space on a room
         * @param {Object} meeple Meeple object
         * @param {Object} room Room object
         */
        moveMeepleToRoom: function (meeple, room)
        { 
            var meepleDiv = 'dgit_player_' + meeple.owner + '_meeple_' + meeple.id;
            var roomSpaceDiv = 'dgit_room_' + room.uiPosition + '_space_' + meeple.uiPosition;
            var roomHighlightDiv = 'dgit_room_' + room.uiPosition + '_space_highlight_' + meeple.uiPosition;
            this.attachToNewParent(meepleDiv, roomSpaceDiv);
            this.slideToObject(meepleDiv, roomSpaceDiv).play();
            dojo.setAttr(roomHighlightDiv, 'meeple', meeple.owner);
        },
    });
});