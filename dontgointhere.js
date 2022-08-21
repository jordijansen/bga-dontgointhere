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
            // for( var player_id in gamedatas.players )
            // {
            //     var player = gamedatas.players[player_id];
                         
            //     // TODO: Setting up players boards if needed
            // }

            debug('setup', 'Create card deck');
            for (var cardNumber = 0; cardNumber < (gamedatas.deckSize/3); cardNumber++)
            {
                dojo.place(
                    this.format_block(
                        'jstpl_deck_card', {
                            card_num: cardNumber,
                        }
                    ), 'dgit_deck'
                );
            }

            debug('setup', 'Create dice');
            for (var dieId in gamedatas.dice)
            {
                var die = gamedatas.dice[dieId];
                if (die.face != HIDDEN) {
                    dojo.removeClass('dgit_die_'+die.value, 'dgit-hidden');
                    dojo.addClass('dgit_die_'+die.value+'_face', die.cssClass);
                }
            }
            
            debug('setup', 'Create room boards');
            for(var faceupRoomsKey in gamedatas.faceupRooms)
            {
                var room = gamedatas.faceupRooms[faceupRoomsKey];
                dojo.addClass('dgit_room_'+room.uiPosition, room.cssClass);
                for(var roomCardsKey in gamedatas.roomCards[room.uiPosition])
                {
                    var card = gamedatas.roomCards[room.uiPosition][roomCardsKey];
                    if(room.type == SECRET_PASSAGE && card.uiPosition == 3) {
                        dojo.addClass('dgit_room_'+room.uiPosition+'_card_'+card.uiPosition, 'dgit-card-back');
                    } else {
                        dojo.addClass('dgit_room_'+room.uiPosition+'_card_'+card.uiPosition, card.cssClass);
                    }
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
