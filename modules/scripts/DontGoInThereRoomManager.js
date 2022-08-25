/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereRoomManager.js
 * 
 * Script to manage room elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
], (dojo, declare) => {
    return declare('dgit.roomManager', ebg.core.gamegui, {
        constructor: function(game) {
            this.game = game;
        },

        /**
         * Setup room info when view is loaded
         * @param {Object} gamedatas array of gamedata
         */
        setup: function (gamedatas) {
            // Create rooms
            for(var faceupRoomsKey in gamedatas.faceupRooms)
            {
                // Create room
                var room = gamedatas.faceupRooms[faceupRoomsKey];
                
                // Add room css class and tooltip
                dojo.addClass('dgit_room_' + room.uiPosition, room.cssClass);
                this.addTooltip('dgit_room_' + room.uiPosition + '_tooltip', room.tooltipText, '');

                // Create cards currently in room
                for(var roomCardsKey in gamedatas.roomCards[room.uiPosition])
                {
                    // Create card
                    var card = gamedatas.roomCards[room.uiPosition][roomCardsKey];
                    this.game.util.placeBlock(ROOM_CARD_TEMPLATE, 'dgit_room_' + room.uiPosition + '_cards',
                        { card_id: card.id, room_number: room.uiPosition, card_number: card.uiPosition, card_css_class: card.cssClass });
                    
                    if (room.type == SECRET_PASSAGE && card.uiPosition == 3 && !this.isPlayerPresentInRoom(gamedatas.meeplesInRooms[room.uiPosition], this.game.getCurrentPlayerId())) {
                        // If room is secret passage flip the 3rd card face down for everyone who has not placed a meeple here
                        dojo.addClass('dgit_room_' + room.uiPosition + '_card_' + card.id, 'dgit-card-back');
                        dojo.setAttr('dgit_room_' + room.uiPosition + '_card_' + card.id, 'special', 'secret-passage');
                        dojo.addClass('dgit_card_' + card.id + '_tooltip', 'dgit-hidden');
                    } else {
                        // If card is faceup show tooltip
                        if (card.tooltipText.length > 0) {
                            this.addTooltip('dgit_card_' + card.id + '_tooltip', card.tooltipText, '');
                        } else {
                            dojo.addClass('dgit_card_' + card.id + '_tooltip', 'dgit-hidden');
                        }
                        
                    }
                }

                // Create meeples currently in room
                for (var roomMeeplesKey in gamedatas.meeplesInRooms[room.uiPosition])
                {
                    var meeple = gamedatas.meeplesInRooms[room.uiPosition][roomMeeplesKey];
                    var meepleDiv = 'dgit_player_' + meeple.owner + '_meeple_' + meeple.id;
                    var roomSpaceDiv = 'dgit_room_' + room.uiPosition + '_space_' + meeple.uiPosition;
                    var roomHighlightDiv = 'dgit_room_' + room.uiPosition + '_space_highlight_' + meeple.uiPosition;
                    this.game.util.placeBlock(MEEPLE_TEMPLATE, roomSpaceDiv,
                        { player_id: meeple.owner, meeple_id: meeple.id, meeple_css_class: meeple.cssClass });
                    this.placeOnObject(meepleDiv, roomSpaceDiv);
                    dojo.setAttr(roomHighlightDiv, 'meeple', meeple.owner);
                }
            }
        },

        /**
         * Check if the current player has a meeple in a room
         * @param {Object} meeplesInRoom List of meeple data in a room
         * @returns {boolean} true = player in room; false = not
         */
        isPlayerPresentInRoom: function (meeplesInRoom)
        {
            for (var meepleKey in meeplesInRoom)
            {
                var meeple = meeplesInRoom[meepleKey];
                if (meeple.owner == this.game.getCurrentPlayerId()) {
                    return true;
                }
            }
            return false;
        },

        /**
         * Reveal the hidden card on the Secret Passage
         */
        revealSecretPassageCard: function ()
        { 
            var secretPassageHiddenCard = dojo.query('div[special="secret-passage"')[0];
            if (secretPassageHiddenCard) {
                dojo.removeClass(secretPassageHiddenCard.id, 'dgit-card-back');
                dojo.removeClass(secretPassageHiddenCard.firstElementChild.id, 'dgit-hidden');
            }
         },
    });
});