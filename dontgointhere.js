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
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.dontgointhere", ebg.core.gamegui, {
        constructor: function(){
            debug('constructor', 'starting constructor');

            this.deckCounter = new ebg.counter();
            this.playerCurseCounters = [];
            this.playerSidePanelCurseCounters = [];
            this.currentPlayerGhosts = new ebg.counter();
            this.currentPlayerSidePanelGhosts = new ebg.counter();
            this.playerDispeledCounters = [];
        },
        
        /**
         * Called when page is loaded (and refreshed). Build elements visible to the player
         * @param {Object} gamedatas Current game data
         */
        setup: function( gamedatas )
        {
            debug('setup', 'Beginning game setup');
            debug('setup::gamedatas', gamedatas);

            debug('setup', 'Definining local constants');
            this.defineGlobalConstants(gamedatas.constants);
            
            // Setting up player boards
            debug('setup', 'Setting up player elements')
            for(var playerKey in gamedatas.playerInfo)
            {
                var player = gamedatas.playerInfo[playerKey];
                debug('setup::player', player);

                dojo.place(
                    this.format_block(
                        'jstpl_player_side_panel', {
                            player_id: player.id,
                            player_natural_order: player.naturalOrder,
                            player_color: player.color,
                        }
                    ), 'player_board_' + player.id
                );

                if (player.id == this.getActivePlayerId())
                {
                    dojo.removeClass('dgit_player_' + player.id + '_active_player', 'dgit-hidden')
                }

                this.playerCurseCounters[player.id] = new ebg.counter();
                this.playerCurseCounters[player.id].create('dgit_player_' + player.id + '_curse_counter');
                this.playerCurseCounters[player.id].setValue(player.curses);
                this.playerSidePanelCurseCounters[player.id] = new ebg.counter();
                this.playerSidePanelCurseCounters[player.id].create('dgit_player_' + player.id + '_side_panel_curse_counter');
                this.playerSidePanelCurseCounters[player.id].setValue(player.curses);

                this.playerDispeledCounters[player.id] = new ebg.counter();
                this.playerDispeledCounters[player.id].create('dgit_player_' + player.id + '_dispeled_counter');
                this.playerDispeledCounters[player.id].setValue(player.cardsDispeled);

                if (player.id == this.getCurrentPlayerId())
                {
                    this.currentPlayerGhosts.create('dgit_player_' + player.id + '_ghost_counter');
                    this.currentPlayerGhosts.setValue(player.ghostTokens);
                    this.currentPlayerSidePanelGhosts.create('dgit_player_' + player.id + '_side_panel_ghost_counter');
                    this.currentPlayerSidePanelGhosts.setValue(player.ghostTokens);
                }

                if (player.cardsDispeled > 0) { 
                    dojo.removeClass('dgit_player_' + player.id + '_dispeled', 'dgit-hidden');
                }
            }

            debug('setup', 'Create card deck');
            if (gamedatas.deckSize == 0) {
                dojo.addClass('dgit_deck', 'dgit-hidden');
            } else {
                this.deckCounter.create('dgit_deck_counter');
                this.deckCounter.setValue(gamedatas.deckSize);
                for(var cardNumber = 0; cardNumber < (gamedatas.deckSize/3); cardNumber++)
                {
                    dojo.place(
                        this.format_block(
                            'jstpl_deck_card', {
                                card_num: cardNumber,
                            }
                        ), 'dgit_deck'
                    );
                }
            }
            

            debug('setup', 'Create dice');
            for (var dieKey in gamedatas.dice)
            {
                var die = gamedatas.dice[dieKey];
                dojo.addClass('dgit_die_'+die.id+'_face', die.cssClass);
            }
            
            debug('setup', 'Create room boards');
            for(var faceupRoomsKey in gamedatas.faceupRooms)
            {
                var room = gamedatas.faceupRooms[faceupRoomsKey];
                dojo.addClass('dgit_room_' + room.uiPosition, room.cssClass);
                this.addTooltip('dgit_room_' + room.uiPosition + '_tooltip', room.tooltipText, '');
                for(var roomCardsKey in gamedatas.roomCards[room.uiPosition])
                {
                    var card = gamedatas.roomCards[room.uiPosition][roomCardsKey];
                    dojo.place(
                        this.format_block(
                            'jstpl_room_card', {
                                card_id: card.id,
                                room_number: room.uiPosition,
                                card_number: card.uiPosition,
                                card_css_class: card.cssClass,
                            }
                        ), 'dgit_room_'+room.uiPosition+'_cards'
                    );
                    if (room.type == SECRET_PASSAGE && card.uiPosition == 3) {
                        console.log('dgit_room_' + room.uiPosition + '_card_' + card.id + '_tooltip');
                        dojo.addClass('dgit_room_' + room.uiPosition + '_card_' + card.id, 'dgit-card-back');
                        dojo.addClass('dgit_card_' + card.id + '_tooltip', 'dgit-hidden');
                    } else {
                        if (card.tooltipText.length > 0) {
                            this.addTooltip('dgit_card_' + card.id + '_tooltip', card.tooltipText, '');
                        } else {
                            dojo.addClass('dgit_card_' + card.id + '_tooltip', 'dgit-hidden');
                        }
                        
                    }
                }
            }

            debug('setup', 'Create meeples in hand');
            for (var meeplesKey in gamedatas.meeplesInHand)
            {
                var meeple = gamedatas.meeplesInHand[meeplesKey];
                dojo.place(
                    this.format_block(
                        'jstpl_meeple', {
                            player_id: meeple.owner,
                            meeple_id: meeple.id,
                            meeple_css_class: meeple.cssClass,
                        }
                    ), 'dgit_player_'+meeple.owner+'_meeples'
                );
            }

            debug('setup', 'Create player cards');
            gamedatas.playerCards.sort((a, b) => (a.type > b.type) ? 1 : -1);
            debug('setup', gamedatas.playerCards);
            for(var playerCardsKey in gamedatas.playerCards)
            {
                var playerCard = gamedatas.playerCards[playerCardsKey];
                dojo.place(
                    this.format_block(
                        'jstpl_player_card', {
                            card_id: playerCard.id,
                            player_id: playerCard.uiPosition,
                            card_css_class: playerCard.cssClass,
                        }
                    ), 'dgit_player_'+playerCard.uiPosition+'_cards'
                );

                console.log('dgit_card_' + playerCard.id + '_tooltip');
                console.log(playerCard.tooltipText);
                if (playerCard.tooltipText.length > 0) {
                    this.addTooltip('dgit_card_' + playerCard.id + '_tooltip', playerCard.tooltipText, '');
                } else {
                    dojo.addClass('dgit_card_' + playerCard.id + '_tooltip', 'dgit-hidden');
                }
            }
            
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            debug('setup', 'Ending game setup');
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
            for(var constant in userConstants)
            {
                if(!globalThis[constant])
                {
                    globalThis[constant] = userConstants[constant];
                }
            }
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
