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
    <div id="dgit_top_panel">
        <div id="dgit_game_panel">
            <div id="dgit_deck"></div>
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
                </div>
                <!-- END die -->
            </div>
        </div>
        <div id="dgit_rooms_panel">
            <!-- BEGIN room -->
            <div id="dgit_room_panel_{ROOM_NUM}" class="dgit-room-panel">
                <div id="dgit_room_{ROOM_NUM}_cards" class="dgit-room-cards">
                    <!-- BEGIN roomcard -->
                    <div id="dgit_room_{ROOM_NUM}_card_{CARD_NUM}" class="dgit-card" style="order: {CARD_NUM}"></div>
                    <!-- END roomcard -->
                </div>
                <div id="dgit_room_{ROOM_NUM}" class="dgit-room" style="order: {ROOM_NUM}"></div>
            </div>
            <!-- END room -->
        </div>
    </div>
    <div id="dgit_bottom_panel">
        <!-- BEGIN playerarea -->
        <div id="dgit_player_{PLAYER_ID}_panel" class="whiteblock dgit-player-panel" style="z-index: -1;">
            <h3 id="dgit_player_{PLAYER_ID}_header" class="dgit-header" style="color: #{PLAYER_COLOR}; background-image: linear-gradient(to right, #2F4F4F, #{PLAYER_COLOR})">
                {PLAYER_NAME}
            </h3>
            <div id="dgit_player_{PLAYER_ID}_dispeled" class="dgit-player-dispeled dgit-hidden"></div>
            <div id="dgit_player_{PLAYER_ID}_cards" class="dgit-player-cards"></div>
        </div>
        <!-- END playerarea -->
    </div>
</div>

<script type="text/javascript">

    var jstpl_deck_card = '<div id="dgit_deck_card_${card_num}" class="dgit-card dgit-card-back dgit-card-in-deck" style="bottom: ${card_num}%"></div>';
    var jstpl_player_card = '<div id="dgit_player_${player_id}_card_${card_id}" class="dgit-card ${card_css_class}"></div>';

</script>  

{OVERALL_GAME_FOOTER}
