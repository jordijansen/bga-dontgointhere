/**
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
        constructor() { },

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
         * Add CSS styling to make an element interactive
         * @param {string} elementId Id of the element
         */
        makeElementInteractive: function (elementId)
        { 
            dojo.addClass(elementId, ['dgit-clickable', 'dgit-highlight']);
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
        },
   });
});