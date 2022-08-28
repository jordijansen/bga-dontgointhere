/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * DontGoInThereUtilities.js
 * 
 * Script of utility functions
 */

var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function(){};
define([
    'dojo',
    'dojo/_base/declare',
    'ebg/core/gamegui',
], (dojo, declare) => {
    return declare('dgit.utilities', ebg.core.gamegui, {
        constructor: function(game) {
            this.game = game;
         },

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

        /**
         * Build the ajax url for an action
         * @param {string} actionName Name of the action
         * @returns {string} ajax url for the action
         */
        getActionUrl: function (actionName)
        { 
            return '/' + this.game.game_name + '/' + this.game.game_name + '/' + actionName + '.html';
        },

        /**
         * Create an html block from a jstpl template and place in parent div
         * @param {string} template Name of template (aka jstpl variable)
         * @param {string} parentDiv Id of div to place block into
         * @param {Object} args Arguments needed by the template
         */
        placeBlock: function (template, parentDiv, args)
        {     
            if (!args) {
                args = [];
            }
             
            dojo.place(this.format_block(template, args), parentDiv);
        },

        /**
         * Remove all styles potentially added for game states
         */
        removeAllTemporaryStyles: function ()
        { 
            dojo.query('.dgit-clickable').removeClass('dgit-clickable');
            dojo.query('.dgit-highlight').removeClass('dgit-highlight');
            dojo.query('.dgit-highlight-card').removeClass('dgit-highlight-card');
        },

        /**
         * Trigger an ajax call for a player action
         * @param {string} actionName Name of the action
         * @param {Object} args Args required for the action 
         */
        triggerPlayerAction: function (actionName, args)
        { 
            // Check if action is possible in current state
            if (this.game.isCurrentPlayerActive()&& this.game.checkAction(actionName)) {
                // Add lock = true to args
                if (!args) {
                    args = [];
                }
                args.lock = true;

                this.game.ajaxcall(this.getActionUrl(actionName), args, this, function (result) {
                }, function (error) {
                    if (error) {
                    }
                });
            }
        },
   });
});