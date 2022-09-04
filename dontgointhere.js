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
            
            this.cardManager = new dgit.cardManager(this);
            this.counterManager = new dgit.counterManager(this);
            this.diceManager = new dgit.diceManager(this);
            this.meepleManager = new dgit.meepleManager(this);
            this.playerManager = new dgit.playerManager(this);
            this.roomManager = new dgit.roomManager(this);
            this.util = new dgit.utilities(this);
        },
        
        /**
         * Called when page is loaded (and refreshed). Build elements visible to the player
         * @param {Object} gamedatas Current game data
         */
        setup: function( gamedatas )
        {
            debug('gamedatas', gamedatas);

            // Define global constants
            this.util.defineGlobalConstants(gamedatas.constants);
            
            // Setup card elements
            this.cardManager.setup(gamedatas);
            // Setup dice elements
            this.diceManager.setup(gamedatas);
            // Setup meeple elements
            this.meepleManager.setup(gamedatas);
            // Setup player elements
            this.playerManager.setup(gamedatas);
            // Setup room elements
            this.roomManager.setup(gamedatas);

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();
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
            debug('STATE', stateName);
            this.util.removeAllTemporaryStyles();
            this.disconnectAll();
            
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
                case ROOM_RESOLUTION_ABILITY:
                    if (this.isCurrentPlayerActive())
                    {
                        var room = args.args.room;

                        // If basement
                        if (room.type == BASEMENT) {
                            dojo.removeClass('dgit_roll_dice_button', 'dgit-hidden');
                            this.connect($('dgit_roll_dice_button'), 'onclick', 'onRollDice');
                        }
                        // If hallway
                        if (room.type == HALLWAY) {
                            var dice = args.args.dice;
                            for (var dieKey in dice) {
                                var die = dice[dieKey];
                                if (die.cssClass != 'dgit-hidden') {
                                    dojo.removeClass('dgit_change_die_button_' + die.id, 'dgit-hidden');
                                }
                                this.connect($('dgit_change_die_button_' + die.id), 'onclick', this.onDieChange(die.id));
                            }
                        }       
                    }
                    break;
                case SELECT_CARD:
                    if (this.isCurrentPlayerActive())
                    {
                        var roomResolving = args.args.roomResolving;
                        dojo.query('div[roomnumber="'+ roomResolving + '"]').addClass('dgit-clickable');
                        dojo.query('div[roomnumber="'+ roomResolving + '"]').addClass('dgit-highlight-card');
                        this.connectClass('dgit-clickable', 'onclick', 'onSelectCard');
                    }
                    break;
                case TRIGGER_CARD_EFFECT:
                    if (this.isCurrentPlayerActive())
                    { 
                        var card = args.args.card;
                        var playerId = card.uiPosition;
                        if (card.type == TOME && $('dgit_player_'+playerId+'_10_cards').children.length % 2 == 1) {
                            for (var type = AMULET; type <= TWIN; type++)
                            { 
                                var dispelButtonDiv = 'dgit_dipel_card_type_button_' + playerId + '_' + type;
                                if (type != TOME) {
                                    dojo.removeClass(dispelButtonDiv, 'dgit-hidden');
                                    this.connect($(dispelButtonDiv), 'onclick', this.onDispelSet(type));
                                }
                            }
                        }
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
            this.util.removeAllTemporaryStyles();
            this.disconnectAll();
            
            switch( stateName )
            {
                case ROOM_RESOLUTION_ABILITY:
                    dojo.addClass('dgit_roll_dice_button', 'dgit-hidden');
                    dojo.query('.dgit-change-die-button').addClass('dgit-hidden');
                    break;
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
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
                    case ROOM_RESOLUTION_ABILITY:
                        if (this.isCurrentPlayerActive()) {
                            this.addActionButton('dgit_skip_ability_button', _('Skip'), 'onSkipAbility');
                            dojo.addClass('dgit_skip_ability_button', 'dgit-important');
                        }
                        break;
                    case 'dummy':
                        break;
                }
            }
        },        

        /***********************************************************************************************
        *    PLAYER ACTIONS::Handle player UX interaction                                              *
        ************************************************************************************************/
        
        /**
         * Triggers when a user clicks an empty room space to place a meeple
         * @param {Object} event onclick event 
         */
        onClickRoomSpace: function (event)
        { 
            dojo.stopEvent(event);

            if (event.target.attributes.meeple.value == 'none') {
                var room = event.target.attributes.room.value;
                var space = event.target.attributes.space.value;

                this.util.triggerPlayerAction(PLACE_MEEPLE, { room: room, space: space });
            }
        },

        /**
         * Triggers when user changes a die to its oppposite face
         * @param {int} dieId database id of die
         * @param {Object} event onclick event
         */
        onDieChange: function (dieId, event) {
            return function (event) { 
                dojo.stopEvent(event);
                this.util.triggerPlayerAction(CHANGE_DIE, { dieId: dieId });
            };
        },

        /**
         * Triggers when user dispels a set of cards from Tome effect
         * @param {int} cardType card type
         * @param {Object} event onclick event
         */
        onDispelSet: function (cardType, event) {
            return function (event) {
                dojo.stopEvent(event);

                for (var type = AMULET; type <= TWIN; type++)
                { 
                    var dispelButtonDiv = 'dgit_dipel_card_type_button_' + this.getCurrentPlayerId() + '_' + type;
                    if (type != TOME) {
                        dojo.addClass(dispelButtonDiv, 'dgit-hidden');
                    }
                }

                this.util.triggerPlayerAction(DISPEL_SET, { cardType: cardType });
            }
        },

        /**
         * Triggers when user initiates a dice roll
         * @param {Object} event onclick event
         */
        onRollDice: function (event)
        { 
            dojo.stopEvent(event);
            this.util.triggerPlayerAction(ROLL_DICE, {});
        },

        /**
         * Triggers when user clicks on a card in a room
         * @param {Object} event onclick event
         */
        onSelectCard: function (event)
        { 
            dojo.stopEvent(event);
            this.util.triggerPlayerAction(TAKE_CARD, { cardId: event.target.attributes['card-id'].value });
        },

        /**
         * Triggers when user skips an optional acttion
         * @param {Object} event onclick event
         */
        onSkipAbility: function (event)
        {
            dojo.stopEvent(event);
            this.util.triggerPlayerAction(SKIP, {});
        },

        /***********************************************************************************************
        *    NOTIFICATIONS::Handle notifications from backend                                          *
        ************************************************************************************************/

        /**
         * Associate notifications with handler methods
         */
        setupNotifications: function()
        {
            dojo.subscribe(ADJUST_GHOSTS, this, 'notif_adjustGhosts');
            dojo.subscribe(CHANGE_DIE, this, 'notif_changeDie');
            dojo.subscribe(CHANGE_PLAYER, this, 'notif_changePlayer');
            dojo.subscribe(DISPEL_CARDS, this, 'notif_dispelCards');
            dojo.subscribe(FLIP_ROOM, this, 'notif_flipRoom');
            dojo.subscribe(FLIP_ROOM_FACEDOWN, this, 'notif_flipRoomFacedown');
            dojo.subscribe(GAIN_CURSES, this, 'notif_gainCurses');
            dojo.subscribe(NEW_CARDS, this, 'notif_newCards');
            dojo.subscribe(PLACE_MEEPLE, this, 'notif_placeMeeple');
            dojo.subscribe(RESET_DICE, this, 'notif_resetDice');
            dojo.subscribe(RETURN_MEEPLE, this, 'notif_returnMeeple');
            dojo.subscribe(REVEAL_WINNERS, this, 'notif_revealWinners');
            dojo.subscribe(REVEAL_PLAYER_ROW, this, 'notif_revealPlayerRow');
            dojo.subscribe(ROLL_DICE, this, 'notif_rollDice');
            dojo.subscribe(SECRET_PASSAGE_REVEAL, this, 'notif_secretPassageReveal');
            dojo.subscribe(TAKE_CARD, this, 'notif_takeCard');
            dojo.subscribe(TRIGGER_MASK, this, 'notif_triggerMask');

            this.notifqueue.setSynchronous(ADJUST_GHOSTS, 500);
            this.notifqueue.setSynchronous(DISPEL_CARDS, 500);
            this.notifqueue.setSynchronous(FLIP_ROOM, 500);
            this.notifqueue.setSynchronous(GAIN_CURSES, 1000);
            this.notifqueue.setSynchronous(NEW_CARDS, 500);
            this.notifqueue.setSynchronous(RETURN_MEEPLE, 500);
            this.notifqueue.setSynchronous(REVEAL_PLAYER_ROW, 1000);
            this.notifqueue.setSynchronous(REVEAL_WINNERS, 6000);
            this.notifqueue.setSynchronous(ROLL_DICE, 500);
            this.notifqueue.setSynchronous(TAKE_CARD, 500);
            this.notifqueue.setSynchronous(TRIGGER_MASK, 500);
        },

        /**
         * Adjust ghost total of current player
         * @param {Object} notification notification object
         */
        notif_adjustGhosts: function (notification)
        { 
            var playerId = notification.args.playerId;
            var amount = notification.args.amount;
            this.playerManager.adjustPlayerGhosts(playerId, amount);
        },

        /**
         * Handle change of die face
         * @param {Object} notification notification object
         */
        notif_changeDie: function (notification)
        { 
            var die = notification.args.die;
            this.diceManager.setDice({ die });
            var delta = die.face == BLANK ? -1 : 1;
            this.counterManager.adjustGhostTotalCounter(delta);
        },

        /**
         * Handle movement of active player token in UI
         * @param {Object} notification notification object
         */
        notif_changePlayer: function (notification)
        { 
            var nextPlayerId = notification.args.nextPlayer;
            this.playerManager.changeActivePlayer(nextPlayerId);
        },

        /**
         * Handle dispeling of cards
         * @param {Object} notification notification object
         */
        notif_dispelCards: function (notification)
        { 
            var player = notification.args.player;
            var cards = notification.args.cards;
            var curseTotal = notification.args.curseTotal;
 
            this.counterManager.adjustPlayerCurses(player, curseTotal);
            this.cardManager.dispelCards(player, cards);
        },

        /**
         * Handle flipping room to its opposite side
         * @param {Object} notification notification object
         */
        notif_flipRoom: function (notification)
        { 
            var currentRoom = notification.args.currentRoom;
            var newRoom = notification.args.newRoom;
            this.roomManager.flipRoom(currentRoom, newRoom);
        },

        /**
         * Handle removing a room out of the game
         * @param {Object} notification notification object
         */
        notif_flipRoomFacedown: function (notification)
        { 
            var room = notification.args.room;
            dojo.destroy('dgit_room_panel_' + room.uiPosition);
        },

        /**
         * Handle gaining curses at end of game
         * @param {Object} notification notification object
         */
        notif_gainCurses: function (notification)
        { 
            var player = notification.args.player;
            var amount = notification.args.amount;
            this.counterManager.adjustPlayerCurses(player, amount);
            dojo.addClass('dgit_score_ghosts_' + player.id, 'dgit-pulse');
        },

        /**
         * Handle drawing cards for new room
         * @param {Object} notification notification object
         */
        notif_newCards: function (notification)
        { 
            var room = notification.args.room;
            var cards = notification.args.cards;
            this.roomManager.createNewRoomCards(room, cards);
        },

        /**
         * Handle placment of meeple in UI
         * @param {Object} notification notification object
         */
        notif_placeMeeple: function (notification)
        { 
            var player = notification.args.player;
            var meeple = notification.args.meeple;
            var room = notification.args.room;
            this.meepleManager.moveMeepleToRoom(player, meeple, room);
        },

        /**
         * Handle return of meeple in UI
         * @param {Object} notification notification object
         */
        notif_returnMeeple: function (notification)
        { 
            var meeple = notification.args.meeple;
            this.meepleManager.moveMeepleToHand(meeple);
        },

        /**
         * Handle resetting dice to unrolled
         * @param {Object} notification notification object
         */
        notif_resetDice: function (notification)
        { 
            var dice = notification.args.dice;
            this.diceManager.resetDice(dice);
        },

        notif_revealPlayerRow: function (notification)
        { 
            var player = notification.args.player;
            var ghosts = notification.args.ghosts;

            // Hide top panel and reveal scoring panel
            dojo.addClass('dgit_top_panel', 'dgit-hidden');
            dojo.removeClass('dgit_end_game_scoring', 'dgit-hidden');            

            // Reveal player row
            dojo.removeClass('dgit_score_row_player_' + player.id, 'dgit-hidden');
            dojo.byId('dgit_score_ghost_counter_' + player.id).textContent = ghosts;
            dojo.addClass('dgit_score_row_player_' + player.id, 'dgit-fade-in');
        },

        notif_revealWinners: function (notification)
        { 
            var winningPlayers = notification.args.winningPlayers;
            for (var winningPlayersKey in winningPlayers)
            {
                var winningPlayer = winningPlayers[winningPlayersKey];
                dojo.addClass('dgit_score_curses_counter_' + winningPlayer.id, 'dgit-pulse');
            }
        },

        /**
         * Handle dice roll
         * @param {Object} notification notification object
         */
        notif_rollDice: function (notification)
        { 
            var diceRolled = notification.args.diceRolled;
            this.diceManager.rollDice(diceRolled);
        },

        /**
         * Handle secret passage card reveal on room resolve
         * @param {Object} notification notification object
         */
        notif_secretPassageReveal: function (notification)
        { 
            this.roomManager.revealSecretPassageCard();
        },

        /**
         * Handle a player taking a card from a room
         * @param {Object} notification notification object
         */
        notif_takeCard: function (notification)
        { 
            var player = notification.args.player;
            var card = notification.args.card;
            var amount = notification.args.amount;
            this.cardManager.moveCardToPlayer(player, card);
            this.counterManager.adjustPlayerCurses(player, amount);
        },

        /**
         * Handle triggering of mask effect
         * @param {Object} notification notification object
         */
        notif_triggerMask: function (notification)
        { 
            var currentPlayer = notification.args.currentPlayer;
            var otherPlayer = notification.args.otherPlayer;
            var ghostAmount = notification.args.ghostAmount;

            this.playerManager.adjustPlayerGhosts(currentPlayer.id, ghostAmount * -1);
            this.playerManager.adjustPlayerGhosts(otherPlayer.id, ghostAmount);
        },
   });             
});
