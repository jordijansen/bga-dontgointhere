{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    dontgointhere_dontgointhere.tpl
    
    DontGoInThere HTML template
-->

<div id="dgit_layout">
    <div id="dgit_end_game_scoring" class="dgit-hidden">
        <div id="dgit_end_game_score_table">
            <!-- BEGIN playerscorerow -->
            <div id="dgit_score_row_player_{PLAYER_ID}" class="dgit-player-score-row" style="background-image: linear-gradient(to right, gray, #{PLAYER_COLOR})">
                <div id="dgit_score_name_{PLAYER_ID}" class="dgit-end-game-name dgit-end-game-text" style="color:#{PLAYER_COLOR}">{PLAYER_NAME}</div>
                <div id="dgit_score_ghosts_{PLAYER_ID}" class="dgit-ghost-token dgit-ghost-token-{PLAYER_NATURAL_ORDER} dgit-ghost-tracker" style="color:#{PLAYER_COLOR}">
                    <span id="dgit_score_ghost_counter_{PLAYER_ID}" class="dgit-player-ghost-counter" style="text-shadow: 2px 0 2px #{PLAYER_COLOR},0 -2px 2px #{PLAYER_COLOR},0 2px 2px #{PLAYER_COLOR},-2px 0 2px #{PLAYER_COLOR};">0</span>
                </div>
                <div id="dgit_score_curses_{PLAYER_ID}" class="dgit-curse-icon" style="color:#{PLAYER_COLOR}">
                    <span id="dgit_score_curse_counter_{PLAYER_ID}" class="dgit-player-curse-counter-game-end">0</span>
                </div>
            </div>
            <!-- END playerscorerow -->
        </div>
    </div>
    <div id="dgit_top_panel">
        <div id="dgit_game_panel">
            <div id="dgit_deck">
                <span id="dgit_deck_counter">0</span>
            </div>
            <div id="dgit_ghost_tokens">
                <!-- BEGIN ghost -->
                <div id="dgit_x_ghost_{GHOST_NUM}" class="dgit-ghost-x" style="z-index: {Z_INDEX}; animation-delay: {DELAY}ms; animation-duration: {X_TIME}s;">
                    <div id="dgit_y_ghost_{GHOST_NUM}" class="dgit-ghost-y" style="animation-delay: {DELAY}ms; animation-duration: {Y_TIME}s;">
                        <div id="dgit_ghost_{GHOST_NUM}" class="dgit-ghost-token dgit-ghost-token-{GHOST_TYPE} dgit-ghost-spin" style="animation-delay: {DELAY}ms; animation-duration: {SPIN_TIME}s;"></div>
                    </div>
                </div>
                <!-- END ghost -->
            </div>
            <div id="dgit_dice_tray">
                <!-- BEGIN die -->
                <div id="dgit_die_{DIE_NUM}" class="dgit-die">
                    <div id="dgit_die_{DIE_NUM}_face" class="dgit-die-face"></div>
                    <button id="dgit_change_die_button_{DIE_NUM}" die="{DIE_NUM}" class="dgit-change-die-button dgit-hidden" href="#">{CHANGE}</button>
                </div>
                <!-- END die -->
                <span id="dgit_dice_total" class="dgit-hidden">0</span>
                <a id="dgit_roll_dice_button" class="action-button bgabutton dgit-hidden" href="#">{ROLL}</a>
            </div>
        </div>
        <div id="dgit_rooms_panel">
            <!-- BEGIN room -->
            <div id="dgit_room_panel_{ROOM_NUM}" class="dgit-room-panel">
                <div id="dgit_room_{ROOM_NUM}_cards" class="dgit-room-cards">
                    <div id="dgit_room_{ROOM_NUM}_card_slot_1" room-number="{ROOM_NUM}" card-slot="1" class="dgit-card-slot"></div>
                    <div id="dgit_room_{ROOM_NUM}_card_slot_2" room-number="{ROOM_NUM}" card-slot="2" class="dgit-card-slot"></div>
                    <div id="dgit_room_{ROOM_NUM}_card_slot_3" room-number="{ROOM_NUM}" card-slot="3" class="dgit-card-slot"></div>
                </div>
                <div id="dgit_room_{ROOM_NUM}" class="dgit-room" style="order: {ROOM_NUM}">
                    <!-- BEGIN roomspace -->
                    <div id="dgit_room_{ROOM_NUM}_space_{SPACE_NUM}" class="dgit-room-space dgit-room-space-{SPACE_NUM}"></div>
                    <div id="dgit_room_{ROOM_NUM}_space_highlight_{SPACE_NUM}" room="{ROOM_NUM}" space="{SPACE_NUM}" meeple="none" class="dgit-room-space-highlight dgit-room-space-highlight-{SPACE_NUM}"></div>
                    <!-- END roomspace -->
                    <span id="dgit_room_{ROOM_NUM}_tooltip" class="dgit-room-tooltip">i</span>
                </div>
            </div>
            <!-- END room -->
        </div>
    </div>
    <div id="dgit_bottom_panel">
        <!-- BEGIN playerarea -->
        <div id="dgit_player_{PLAYER_ID}_panel" class="whiteblock dgit-player-panel" style="z-index: -1;">
            <h3 id="dgit_player_{PLAYER_ID}_header" class="dgit-header" style="color: #{PLAYER_COLOR}; background-image: linear-gradient(to right, gray, #{PLAYER_COLOR})">
                {PLAYER_NAME}
            </h3>
            <div id="dgit_player_{PLAYER_ID}_info_panel" class="dgit-player-info-panel">
                <div id="dgit_player_{PLAYER_ID}_tracker_panel" class="dgit-player-tracker-panel">
                    <div id="dgit_player_{PLAYER_ID}_curse_tracker" class="dgit-curse-icon"></div>
                    <span id="dgit_player_{PLAYER_ID}_curse_counter" class="dgit-player-curse-counter">0</span>
                    <div id="dgit_player_{PLAYER_ID}_ghost_tracker" class="dgit-ghost-token dgit-ghost-token-{PLAYER_NATURAL_ORDER} dgit-ghost-tracker-animate"></div>
                    <span id="dgit_player_{PLAYER_ID}_ghost_counter" class="dgit-player-ghost-counter" style="text-shadow: 2px 0 2px #{PLAYER_COLOR},0 -2px 2px #{PLAYER_COLOR},0 2px 2px #{PLAYER_COLOR},-2px 0 2px #{PLAYER_COLOR};">?</span>
                </div>
                <div id="dgit_player_{PLAYER_ID}_meeples" class="dgit-player-meeples" style="background-color: #{PLAYER_COLOR}; border-color: #{PLAYER_COLOR}; color: #{PLAYER_COLOR}"></div>
            </div>
            <div id="dgit_player_{PLAYER_ID}_cards_panel" class="dgit-player-cards-panel">
                <div id="dgit_player_{PLAYER_ID}_dispeled" class="dgit-player-dispeled dgit-hidden">
                    <span id="dgit_player_{PLAYER_ID}_dispeled_counter" class="dgit-player-dispeled-counter" style="text-shadow: 5px 0 5px #{PLAYER_COLOR},0 -5px 5px #{PLAYER_COLOR},0 5px 5px #{PLAYER_COLOR},-5px 0 5px #{PLAYER_COLOR};">0</span>
                    <div id="dgit_player_{PLAYER_ID}_dispeled_cards" class="dgit-card dgit-card-back"></div>
                </div>
                <div id="dgit_player_{PLAYER_ID}_cards" class="dgit-player-cards">
                    <!-- BEGIN playercardtype -->
                    <div id="dgit_player_{PLAYER_ID}_{CARD_TYPE}_cards" class="dgit-player-card-type-section dgit-hidden" style="order: {CARD_TYPE}">
                        <button id="dgit_dipel_card_type_button_{PLAYER_ID}_{CARD_TYPE}" cardtype="{CARD_TYPE}" class="dgit-dispel-card-type-button dgit-hidden" href="#">{DISPEL}</button>
                    </div>
                    <!-- END playercardtype -->
                </div>
            </div>
        </div>
        <!-- END playerarea -->
    </div>
</div>

<script type="text/javascript">

    var jstpl_cursed_card = '<div id="dgit_card_${card_id}" class="dgit-card ${card_css_class}" card-id="${card_id}" roomnumber="${room_ui_position}" curses="${curses}" special="false">\
                                <span id="dgit_card_${card_id}_tooltip" class="dgit-card-tooltip">i</span>\
                            </div>';
    var jstpl_deck_card = '<div id="dgit_deck_card_${card_num}" class="dgit-card dgit-card-back dgit-card-in-deck" style="bottom: ${card_num}%"></div>';
    var jstpl_ghost_token = '<div id="dgit_moving_ghost" class="dgit-ghost-token dgit-ghost-token-${ghost_type}" style="top: 50%; left: 50%;"></div>';
    var jstpl_meeple = '<div id="dgit_meeple_${meeple_id}" type="${meeple_type}" owner="${meeple_owner}" class="dgit-meeple ${meeple_css_class}"></div>';
    var jstpl_player_side_panel = '<div id="dgit_player_${player_id}_side_panel" class="dgit-player-side-panel">\
                                        <div id="dgit_player_${player_id}_active_player" class="dgit-active-player dgit-hidden"></div>\
                                        <div id="dgit_player_${player_id}_curse_count" class="dgit-curse-icon">\
                                            <span id="dgit_player_${player_id}_side_panel_curse_counter" class="dgit-player-side-panel-curse-counter">0</span>\
                                        </div>\
                                        <div id="dgit_player_${player_id}_ghost_count" class="dgit-ghost-token dgit-ghost-token-${player_natural_order} dgit-ghost-tracker">\
                                            <span id="dgit_player_${player_id}_side_panel_ghost_counter" class="dgit-player-ghost-counter" style="text-shadow: 2px 0 2px #${player_color},0 -2px 2px #${player_color},0 2px 2px #${player_color},-2px 0 2px #${player_color};">?</span>\
                                        </div>\
                                   </div>';

</script>  

{OVERALL_GAME_FOOTER}
