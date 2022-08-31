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
    'dojo/on',
    'ebg/core/gamegui',
], (dojo, declare, on) => {
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

                dojo.setStyle('dgit_deck_counter', 'bottom', gamedatas.deckSize / 3 + '%');
            }

            // Create player cards
            for(var playerCardsKey in gamedatas.playerCards)
            {
                // Place card
                var playerCard = gamedatas.playerCards[playerCardsKey];
                this.game.util.placeBlock(CURSED_CARD_TEMPLATE, 'dgit_player_' + playerCard.uiPosition + '_' + playerCard.type + '_cards',
                    { card_id: playerCard.id, player_id: playerCard.uiPosition, card_css_class: playerCard.cssClass, room_ui_position: -1, card_ui_position: playerCard.curses });
                dojo.removeClass('dgit_player_' + playerCard.uiPosition + '_' + playerCard.type + '_cards', 'dgit-hidden');

                // Create tooltip
                if (playerCard.tooltipText.length > 0) {
                    // If card has a tooltip, create it
                    this.game.addTooltip('dgit_card_' + playerCard.id + '_tooltip', playerCard.tooltipText, '');
                } else {
                    // Else hide tooltip element
                    dojo.addClass('dgit_card_' + playerCard.id + '_tooltip', 'dgit-hidden');
                }
            }
        },

        /**
         * Dispel a list of cards from a player area
         * @param {Object} player player object
         * @param {Object} cards list of cards to dispel
         */
        dispelCards: function (player, cards)
        { 
            var dispeledCardsDiv = 'dgit_player_' + player.id + '_dispeled';
            var cardType = cards[0].type;
            var cardTypeDiv = 'dgit_player_' + player.id + '_' + cardType + '_cards';
            dojo.removeClass(dispeledCardsDiv, 'dgit-hidden');
            var delay = 0;
            var moveCard = {};
            var currentCardsOfType = $(cardTypeDiv).children.length;
            if ($(cardTypeDiv).querySelector('.dgit-dispel-card-type-button') !== null) {
                currentCardsOfType = currentCardsOfType - 1;
            }
            
            for (var cardKey in cards) {
                var card = cards[cardKey];
                var cardDiv = 'dgit_card_' + card.id;
                moveCard[cardKey] = this.game.slideToObject(cardDiv, dispeledCardsDiv, 500, delay).play();
                on(moveCard[cardKey], "End", function (cardDiv) {
                    dojo.destroy(cardDiv);
                });
                delay = delay + 200;
            }
            
            if (currentCardsOfType == cards.length) {
                dojo.addClass(cardTypeDiv, 'dgit-hidden');
            }
            
            this.game.counterManager.adjustPlayerDispeledCounter(player, cards.length);
        },

        /**
         * Move a card to a players tableau
         * @param {Object} player 
         * @param {Object} card 
         */
        moveCardToPlayer: function (player, card)
        { 
            var cardDiv = 'dgit_card_' + card.id;
            var playerCardDiv = 'dgit_player_' + player.id + '_' + card.type + '_cards';
            dojo.setAttr(cardDiv, 'roomnumber', -1);
            dojo.setStyle(cardDiv, 'order', card.curses);
            dojo.removeClass(playerCardDiv, 'dgit-hidden');
            this.game.attachToNewParent(cardDiv, playerCardDiv);
            var moveCard = this.game.slideToObject(cardDiv, playerCardDiv).play();
            on(moveCard, "End", function () {
                $(cardDiv).style.removeProperty('top');
                $(cardDiv).style.removeProperty('left');
            });
        },
    });
});