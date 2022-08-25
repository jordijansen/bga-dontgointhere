/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereCardManager.js
 * 
 * Script to manage card elements
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
], (dojo, declare) => {
    return declare('dgit.cardManager', null, {
        constructor: function(game) {
            this.game = game;
        },

        /**
         * Setup card info on page load
         * @param {Object} gamedatas Array of game data
         */
        setup: function (gamedatas)
        { 
            // Create card deck
            if (gamedatas.deckSize == 0) {
                dojo.addClass('dgit_deck', 'dgit-hidden');
            } else {
                this.game.counterManager.createDeckCounter(gamedatas.deckSize);

                // Create face down cards to simulate deck size visually
                for(var cardNumber = 0; cardNumber < (gamedatas.deckSize/3); cardNumber++)
                {
                    this.game.util.placeBlock(DECK_CARD_TEMPLATE, 'dgit_deck', { card_num: cardNumber });
                }
            }

            // Sort player cards by type so cards of same type are adjacent
            gamedatas.playerCards.sort((a, b) => (a.type > b.type) ? 1 : -1);

            // Create player cards
            for(var playerCardsKey in gamedatas.playerCards)
            {
                // Place card
                var playerCard = gamedatas.playerCards[playerCardsKey];
                this.game.util.placeBlock(PLAYER_CARD_TEMPLATE, 'dgit_player_' + playerCard.uiPosition + '_cards',
                    { card_id: playerCard.id, player_id: playerCard.uiPosition, card_css_class: playerCard.cssClass } );

                // Create tooltip
                if (playerCard.tooltipText.length > 0) {
                    // If card has a tooltip, create it
                    this.addTooltip('dgit_card_' + playerCard.id + '_tooltip', playerCard.tooltipText, '');
                } else {
                    // Else hide tooltip element
                    dojo.addClass('dgit_card_' + playerCard.id + '_tooltip', 'dgit-hidden');
                }
            }
        },
    });
});