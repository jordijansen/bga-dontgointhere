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
    g_gamethemeurl + 'modules/scripts/DontGoInThereCardManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInThereCounterManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInThereDiceManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInThereMeepleManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInTherePlayerManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInThereRoomManager.js',
    g_gamethemeurl + 'modules/scripts/DontGoInThereUtilities.js',
], function (dojo, declare) {
    return declare("bgagame.dontgointhere", ebg.core.gamegui, {
        constructor: function(){
            debug('game::constructor::', 'Starting constructor');
            
            this.cardManager = new dgit.cardManager();
            this.counterManager = new dgit.counterManager();
            this.diceManager = new dgit.diceManager();
            this.meepleManager = new dgit.meepleManager();
            this.playerManager = new dgit.playerManager();
            this.roomManager = new dgit.roomManager();
            this.util = new dgit.utilities();
        },
        
        /**
         * Called when page is loaded (and refreshed). Build elements visible to the player
         * @param {Object} gamedatas Current game data
         */
        setup: function( gamedatas )
        {
            // Initial debug logging
            debug('setup', 'Beginning game setup');
            debug('setup', gamedatas);

            // Define global constants
            this.util.defineGlobalConstants(gamedatas.constants);
            
            // Setup card elements
            this.cardManager.setup(gamedatas);
            // Setup dice elements
            this.diceManager.setup(gamedatas);
            // Setup meeple elements
            this.meepleManager.setup(gamedatas);
            // Setup player elements
            this.playerManager.setup(gamedatas, this.getActivePlayerId(), this.getCurrentPlayerId());
            // Setup room elements
            this.roomManager.setup(gamedatas);

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
            debug('onEnteringState', stateName);
            debug('onEnteringState', args);
            
            switch( stateName )
            {
                case PLAYER_TURN:
                    if (this.isCurrentPlayerActive())
                    { 
                        dojo.query('div[meeple="none"]').addClass('dgit-clickable');
                        dojo.query('div[meeple="none"]').addClass('dgit-highlight');
                        this.connectClass('dgit-room-space-highlight', 'onclick', 'onClickRoomSpace');
                    }
                    break;
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

            this.util.removeAllTemporaryStyles();
            this.disconnectAll();
            
            switch( stateName )
            {
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
                    case 'dummy':
                        break;
                }
            }
        },        

        /***********************************************************************************************
        *    PLAYER ACTIONS::Handle player UX interaction                                              *
        ************************************************************************************************/
        /**
         * Build the ajax url for an action
         * @param {string} actionName Name of the action
         * @returns {string} ajax url for the action
         */
        getActionUrl: function (actionName)
        { 
            return '/' + this.game_name + '/' + this.game_name + '/' + actionName + '.html';
        },
        
        /**
         * Triggers when a user clicks an empty room space to place a meeple
         * @param {Object} event onclick event 
         */
        onClickRoomSpace: function (event)
        { 
            dojo.stopEvent(event);

            if (this.isCurrentPlayerActive() && event.target.attributes.meeple.value == 'none') {
                var room = event.target.attributes.room.value;
                var space = event.target.attributes.space.value;

                this.triggerPlayerAction(PLACE_MEEPLE, { room: room, space: space });
            }
        },

        /**
         * Trigger an ajax call for a player action
         * @param {string} actionName Name of the action
         * @param {Object} args Args required for the action 
         */
        triggerPlayerAction: function (actionName, args)
        { 
            // Check if action is possible in current state
            if (this.checkAction(actionName)) {
                // Add lock = true to args
                if (!args) {
                    args = [];
                }
                args.lock = true;

                this.ajaxcall(this.getActionUrl(actionName), args, this, function (result) {
                }, function (error) {
                    if (error) {
                    }
                });
            }
        },


        /***********************************************************************************************
        *    NOTIFICATIONS::Handle notifications from backend                                          *
        ************************************************************************************************/

        /**
         * Associate notifications with handler methods
         */
        setupNotifications: function()
        {
            debug('setupNotifications', 'Setting up notification subscriptions');

            dojo.subscribe(PLACE_MEEPLE, this, 'notif_placeMeeple');
        },

        notif_placeMeeple: function (notification)
        { 
            var meeple = notification.args.meeple;
            var room = notification.args.room;

            this.meepleManager.moveMeepleToRoom(meeple, room);
        },
   });             
});
