/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * dontgointhere.js
 *
 * DontGoInThere user interface script
 *
 */

 var isDebug = window.location.host == 'studio.boardgamearena.com';
 var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    "dojo",
    "dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    g_gamethemeurl + 'modules/scripts/CounterManager.js',
], function (dojo, declare) {
    return declare("bgagame.dontgointhere", ebg.core.gamegui, {
        constructor: function(){
            debug('game::constructor::', 'Starting constructor');
                
            this.counterManager = new dgit.counterManager();
        },
        
        /**
         * Called when page is loaded (and refreshed). Build elements visible to the player
         * @param {Object} gamedatas Current game data
         */
        setup: function( gamedatas )
        {
            // Initial debug logging
            var debugLogTag = 'game::setup::';
            debug(debugLogTag, 'Beginning game setup');
            debug(debugLogTag+'gamedatas', gamedatas);

            // Define global constants
            debug(debugLogTag, 'Defining global constants');
            this.defineGlobalConstants(gamedatas.constants);
            
            // Setup player elements
            debug(debugLogTag, 'Setting up elements for all players')
            for(var playerKey in gamedatas.playerInfo)
            {
                var player = gamedatas.playerInfo[playerKey];
                debug(debugLogTag+'Setting up player elements', player);

                // Place custom block in player panel
                debug(debugLogTag, 'Placing custom block in player panel')
                this.placeBlock('jstpl_player_side_panel', 'player_board_' + player.id,
                    { player_id: player.id, player_natural_order: player.naturalOrder, player_color: player.color });

                // Unhide active player marker for first player
                debug(debugLogTag, 'Unhiding active player marker for first player')
                if (player.id == this.getActivePlayerId())
                {
                    dojo.removeClass('dgit_player_' + player.id + '_active_player', 'dgit-hidden')
                }

                // Create player related counters
                debug(debugLogTag, 'Creating player related counters')
                this.counterManager.createPlayerCurseCounters(player);
                this.counterManager.createPlayerDispeledCounter(player);
                if (player.id == this.getCurrentPlayerId())
                {
                    this.counterManager.createPlayerGhostCounters(player);
                }

                // If player has dispeled cards, show dispeled card element
                if (player.cardsDispeled > 0) { 
                    debug(debugLogTag, 'Unhiding dispeled card element')
                    dojo.removeClass('dgit_player_' + player.id + '_dispeled', 'dgit-hidden');
                }
            }

            // Create card deck
            debug(debugLogTag, 'Creating card deck');
            if (gamedatas.deckSize == 0) {
                dojo.addClass('dgit_deck', 'dgit-hidden');
            } else {
                this.counterManager.createDeckCounter(gamedatas.deckSize);

                // Create face down cards to simulate deck size visually
                for(var cardNumber = 0; cardNumber < (gamedatas.deckSize/3); cardNumber++)
                {
                    this.placeBlock('jstpl_deck_card', 'dgit_deck', { card_num: cardNumber });
                }
            }
            
            // Create dice
            debug(debugLogTag, 'Creating dice');
            for (var dieKey in gamedatas.dice)
            {
                var die = gamedatas.dice[dieKey];
                dojo.addClass('dgit_die_'+die.id+'_face', die.cssClass);
            }
            
            // Create rooms
            debug(debugLogTag, 'Creating rooms');
            for(var faceupRoomsKey in gamedatas.faceupRooms)
            {
                // Create room
                var room = gamedatas.faceupRooms[faceupRoomsKey];
                debug(debugLogTag + 'Creating room', room);
                
                // Add room css class and tooltip
                debug(debugLogTag, 'Adding CSS and tooltip to room')
                dojo.addClass('dgit_room_' + room.uiPosition, room.cssClass);
                this.addTooltip('dgit_room_' + room.uiPosition + '_tooltip', room.tooltipText, '');

                // Create cards currently in room
                debug(debugLogTag, 'Creating cards in room')
                for(var roomCardsKey in gamedatas.roomCards[room.uiPosition])
                {
                    // Create card
                    var card = gamedatas.roomCards[room.uiPosition][roomCardsKey];
                    debug(debugLogTag + 'Creating card in room', card);
                    this.placeBlock('jstpl_room_card', 'dgit_room_' + room.uiPosition + '_cards',
                        { card_id: card.id, room_number: room.uiPosition, card_number: card.uiPosition, card_css_class: card.cssClass });
                    
                    if (room.type == SECRET_PASSAGE && card.uiPosition == 3) {
                        // If room is secret passage flip the 3rd card face down for everyone who has not placed a meeple here
                        debug(debugLogTag, 'Flipping third card in Secret Passage face down');
                        dojo.addClass('dgit_room_' + room.uiPosition + '_card_' + card.id, 'dgit-card-back');
                        dojo.addClass('dgit_card_' + card.id + '_tooltip', 'dgit-hidden');
                    } else {
                        // If card is faceup show tooltip
                        debug(debugLogTag, 'Creating card tooltip');
                        if (card.tooltipText.length > 0) {
                            this.addTooltip('dgit_card_' + card.id + '_tooltip', card.tooltipText, '');
                        } else {
                            dojo.addClass('dgit_card_' + card.id + '_tooltip', 'dgit-hidden');
                        }
                        
                    }
                }
            }

            // Create meeples in player hands
            debug(debugLogTag, 'Creating meeples in hand');
            for (var meeplesKey in gamedatas.meeplesInHand)
            {
                // Create meeple
                var meeple = gamedatas.meeplesInHand[meeplesKey];
                debug(debugLogTag + 'Creating meeple', meeple);

                // Place meeple
                debug(debugLogTag, 'Placing meeple');
                this.placeBlock('jstpl_meeple', 'dgit_player_' + meeple.owner + '_meeples',
                    { player_id: meeple.owner, meeple_id: meeple.id, meeple_css_class: meeple.cssClass });
            }

            // TODO: Handle meeples in rooms

            // Create player cards
            debug(debugLogTag + 'Creating player cards', gamedatas.playerCards);

            // Sort cards by type so cards of same type are adjacent
            debug(debugLogTag + 'Sorting player cards');
            gamedatas.playerCards.sort((a, b) => (a.type > b.type) ? 1 : -1);


            for(var playerCardsKey in gamedatas.playerCards)
            {
                // Place card
                debug(debugLogTag + 'Creating player card', playerCard);
                var playerCard = gamedatas.playerCards[playerCardsKey];
                this.placeBlock('jstpl_player_card', 'dgit_player_' + playerCard.uiPosition + '_cards',
                    { card_id: playerCard.id, player_id: playerCard.uiPosition, card_css_class: playerCard.cssClass } );

                // Create tooltip
                debug(debugLogTag, 'Creating tooltip');
                if (playerCard.tooltipText.length > 0) {
                    // If card has a tooltip, create it
                    this.addTooltip('dgit_card_' + playerCard.id + '_tooltip', playerCard.tooltipText, '');
                } else {
                    // Else hide tooltip element
                    dojo.addClass('dgit_card_' + playerCard.id + '_tooltip', 'dgit-hidden');
                }
            }
            
            // Setup game notifications to handle (see "setupNotifications" method below)
            debug(debugLogTag, 'Setting up notifications');
            this.setupNotifications();

            debug(debugLogTag, 'Ending game setup');
        },
       

        /***********************************************************************************************
        *    GAME STATE FUNCTIONS::Methods to handle game state transitions                            *
        ************************************************************************************************/
        
        /**
         * Perform interface changes when entering a new state
         * @param {string} stateName Name of state that is being entered
         * @param {Object} args Any arguments needed for interface updates
         */
        onEnteringState: function( stateName, args )
        {
            debug('onEnteringState', 'Entering a new state');
            debug('onEnteringState::stateName', stateName);
            debug('onEnteringState::args', args);
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }
        },

        /**
         * Perform interface changes when leaving a game state
         * @param {string} stateName Name of the state that is being left
         */
        onLeavingState: function( stateName )
        {
            debug('onLeavingState', 'Leaving a state');
            debug('onLeavingState::stateName', stateName);
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
        }, 

        /**
         * Manages displayed action buttons when entering a new state
         * @param {string} stateName Name of state that is being entered
         * @param {Object} args Any arguments needed for interface updates
         */
        onUpdateActionButtons: function( stateName, args )
        {
            debug('onUpdateActionButtons', 'Updating action buttons');
            debug('onUpdateActionButtons::stateName', stateName);
            debug('onUpdateActionButtons::args', args);
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        /***********************************************************************************************
        *    UTILITY FUNCTIONS::Generic utility methods                                                *
        ************************************************************************************************/
        
        /**
         * Gives javascript access to constants defined in PHP
         * @param {Object} userConstants Defined user constants
         */
        defineGlobalConstants: function(userConstants)
        {
            debug('game::defineGlobalConstants::userConstants', userConstants);
            for(var constant in userConstants)
            {
                if(!globalThis[constant])
                {
                    globalThis[constant] = userConstants[constant];
                }
            }
        },

        /**
         * Create an html block from a jstpl template and place in parent div
         * @param {string} template Name of template (aka jstpl variable)
         * @param {string} parentDiv Id of div to place block into
         * @param {Object} args Arguments needed by the template
         */
        placeBlock: function (template, parentDiv, args)
        { 
            var debugLogTag = 'game::placeblock::';
            debug(debugLogTag, 'Placing HTML block');
            debug(debugLogTag + 'template', template);
            debug(debugLogTag + 'parentDiv', parentDiv);
            debug(debugLogTag + 'args', args);

            if (!args) {
                args = [];
            }
            
            dojo.place(this.format_block(template, args), parentDiv);
        },


        /***********************************************************************************************
        *    PLAYER ACTIONS::Handle player UX interaction                                              *
        ************************************************************************************************/
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }

            this.ajaxcall( "/dontgointhere/dontgointhere/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },        
        
        */


        /***********************************************************************************************
        *    NOTIFICATIONS::Handle notifications from backend                                          *
        ************************************************************************************************/

        /**
         * Associate notifications with handler methods
         */
        setupNotifications: function()
        {
            debug('setupNotifications', 'Setting up notification subscriptions');
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
   });             
});
