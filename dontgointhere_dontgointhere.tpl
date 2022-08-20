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
        <div id="dgit_deck_and_dice">
            <div id="dgit_deck"></div>
            <div id="dgit_dice_tray"></div>
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
</div>

<script type="text/javascript">

    var jstpl_deck_card = '<div id="dgit_deck_card_${card_num}" class="dgit-card dgit-card-back dgit-card-in-deck" style="bottom: ${card_num}%"></div>';

</script>  

{OVERALL_GAME_FOOTER}
