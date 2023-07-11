<?php
 /**
  *------
  * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
  * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * -----
  * 
  * dontgointhere.game.php
  *
  * The main file for the game logic.
  *
  */

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );
require_once("modules/constants.inc.php");
require_once("modules/DontGoInThereCardManager.class.php");
require_once("modules/DontGoInThereDiceManager.class.php");
require_once("modules/DontGoInThereMeepleManager.class.php");
require_once("modules/DontGoInTherePlayerManager.class.php");
require_once("modules/DontGoInThereRoomManager.class.php");
class DontGoInThere extends Table
{
    function __construct()
    {
        parent::__construct();

        self::initGameStateLabels(array(
            CLOCKS_COLLECTED => 10,
            GHOSTS_ROLLED => 11,
            LAST_SELECTED_CARD => 12,
            RESOLVED_ROOM_ABILITY => 13,
            RESOLVED_ROOM_GHOSTS => 14,
            ROOM_RESOLVER => 15,
            ROOM_RESOLVING => 16,
            SECRET_PASSAGE_REVEALED => 17,
            TOTAL_TURNS => 18,
            TURN_COUNTER => 19,
            // OPTIONS
            CURSED_CARDS_OPTION => CURSED_CARDS_OPTION_ID,
            CURSED_CARDS_1 => CURSED_CARDS_1_ID,
            CURSED_CARDS_2 => CURSED_CARDS_2_ID,
            CURSED_CARDS_3 => CURSED_CARDS_3_ID,
            CURSED_CARDS_4 => CURSED_CARDS_4_ID,
            CURSED_CARDS_5 => CURSED_CARDS_5_ID,
            CURSED_CARDS_6 => CURSED_CARDS_6_ID,
            CURSED_CARDS_7 => CURSED_CARDS_7_ID,
            CURSED_CARDS_8 => CURSED_CARDS_8_ID,

        ));

        $this->cardManager = new DontGoInThereCardManager($this);
        $this->diceManager = new DontGoInThereDiceManager($this);
        $this->meepleManager = new DontGoInThereMeepleManager($this);
        $this->playerManager = new DontGoInTherePlayerManager($this);
        $this->roomManager = new DontGoInThereRoomManager($this);
    }

    /**
     * Get name of game
     * @return string Name value of the game
     */
    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "dontgointhere";
    }

    /**
     * Initial setup of the game
     * @param array $players An array of players
     * @param array $options An array of game options
     * @return void
     */
    protected function setupNewGame($players, $options = array())
    {
        // Setup players
        $this->playerManager->setupNewGame($players);
        // Setup meeples
        $this->meepleManager->setupNewGame($this->playerManager->getPlayers());
        // Setup dice
        $this->diceManager->setupNewGame();
        // Setup rooms
        $this->roomManager->setupNewGame();
        // Setup cards
        $this->cardManager->setupNewGame($this->playerManager->getPlayerCount(), $this->roomManager->getLibraryPosition(), $this->CURSE_TYPE_LABEL);

        // Initialize global variables
        self::setGameStateInitialValue(CLOCKS_COLLECTED, DGIT_FALSE);
        self::setGameStateInitialValue(GHOSTS_ROLLED, -1);
        self::setGameStateInitialValue(LAST_SELECTED_CARD, -1);
        self::setGameStateInitialValue(RESOLVED_ROOM_ABILITY, DGIT_FALSE);
        self::setGameStateInitialValue(RESOLVED_ROOM_GHOSTS, DGIT_FALSE);
        self::setGameStateInitialValue(ROOM_RESOLVER, 0);
        self::setGameStateInitialValue(ROOM_RESOLVING, 0);
        self::setGameStateInitialValue(SECRET_PASSAGE_REVEALED, DGIT_FALSE);
        self::setGameStateInitialValue(TOTAL_TURNS, $this->playerManager->getPlayerCount() * 12);
        self::setGameStateInitialValue(TURN_COUNTER, 0);

        // Init game statistics
        self::initStat( 'table', 'turns_number', $this->playerManager->getPlayerCount() * 12 );
        self::initStat( 'table', 'avg_curses', 0 );
        self::initStat( 'table', 'avg_ghosts', 0 );
        self::initStat( 'table', 'avg_cards_dispeled', 0 );
        self::initStat( 'table', 'avg_curses_dispeled', 0 );

        self::initStat( 'player', 'turns_number', 12 );
        self::initStat( 'player', 'curses_taken', 0 );
        self::initStat( 'player', 'curses_dispeled', 0 );
        self::initStat( 'player', 'ghosts_taken', 0 );
        self::initStat( 'player', 'ghosts_discarded', 0 );
        self::initStat( 'player', 'amulets_taken', 0 );
        self::initStat( 'player', 'cats_taken', 0 );
        self::initStat( 'player', 'clocks_taken', 0 );
        self::initStat( 'player', 'dolls_taken', 0 );
        self::initStat( 'player', 'holy_water_taken', 0 );
        self::initStat( 'player', 'masks_taken', 0 );
        self::initStat( 'player', 'mirrors_taken', 0 );
        self::initStat( 'player', 'music_boxes_taken', 0 );
        self::initStat( 'player', 'portraits_taken', 0 );
        self::initStat( 'player', 'rings_taken', 0 );
        self::initStat( 'player', 'tomes_taken', 0 );
        self::initStat( 'player', 'twins_taken', 0 );
        self::initStat( 'player', 'amulets_dispeled', 0 );
        self::initStat( 'player', 'cats_dispeled', 0 );
        self::initStat( 'player', 'clocks_dispeled', 0 );
        self::initStat( 'player', 'dolls_dispeled', 0 );
        self::initStat( 'player', 'holy_water_dispeled', 0 );
        self::initStat( 'player', 'masks_dispeled', 0 );
        self::initStat( 'player', 'mirrors_dispeled', 0 );
        self::initStat( 'player', 'music_boxes_dispeled', 0 );
        self::initStat( 'player', 'portraits_dispeled', 0 );
        self::initStat( 'player', 'rings_dispeled', 0 );
        self::initStat( 'player', 'tomes_dispeled', 0 );
        self::initStat( 'player', 'twins_dispeled', 0 );
        self::initStat( 'player', 'amulets_curses', 0 );
        self::initStat( 'player', 'cats_curses', 0 );
        self::initStat( 'player', 'clocks_curses', 0 );
        self::initStat( 'player', 'dolls_curses', 0 );
        self::initStat( 'player', 'holy_water_curses', 0 );
        self::initStat( 'player', 'masks_curses', 0 );
        self::initStat( 'player', 'mirrors_curses', 0 );
        self::initStat( 'player', 'music_boxes_curses', 0 );
        self::initStat( 'player', 'portraits_curses', 0 );
        self::initStat( 'player', 'rings_curses', 0 );
        self::initStat( 'player', 'tomes_curses', 0 );
        self::initStat( 'player', 'twins_curses', 0 );

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

    /************ End of the game initialization *****/
    }

    /**
     * Gather all informations about current game situation (visible by the current player)
     * @return array An array of game data
     */
    protected function getAllDatas()
    {
        $currentPlayerId = self::getCurrentPlayerId();

        $roomCards = [];
        $roomMeeples = [];
        for ($roomNumber = 1; $roomNumber <= 3; $roomNumber++)
        {
            $roomCards[$roomNumber] = $this->cardManager->getUiData(ROOM_PREPEND . $roomNumber);
            $roomMeeples[$roomNumber] = $this->meepleManager->getUiData(ROOM_PREPEND . $roomNumber);
        }

        $data = [
            'constants' => get_defined_constants(true)['user'],
            'deckSize' => $this->cardManager->countCursedCards(DECK),
            'dice' => $this->diceManager->getUiData(),
            'faceupRooms' => $this->roomManager->getUiData(FACEUP),
            'facedownRooms' => $this->roomManager->getUiData(FACEDOWN),
            'meeplesInHand' => $this->meepleManager->getUiData(HAND),
            'meeplesInRooms' => $roomMeeples,
            'playerCards' => $this->cardManager->getUiData(HAND),
            'playerInfo' => $this->playerManager->getUiData($currentPlayerId),
            'roomCards' => $roomCards,
            'roomResolving' => $this->roomManager->getRoomResolving(),
            'secretPassageRevealed' => $this->roomManager->getSecretPassageRevealed(),
        ];
        return $data;
    }

    /**
     * Compute and return the current game progression.
     * @return int Percentage of game completed (between 0 and 100)
     */
    function getGameProgression()
    {
        return self::getGameStateValue(TURN_COUNTER) / self::getGameStateValue(TOTAL_TURNS) * 100;
    }


    /***********************************************************************************************
     *    UTILITY FUNCTIONS::Generic utility methods                                                *
     ************************************************************************************************/

    /**
     * Wrapper for getCurrentPlayerId
     * @return int ID of player who loaded screen
     */
    function getViewingPlayerId()
    {
        return self::getCurrentPlayerId();
    }

    /**
     * Increment turn counter for game progression calculation
     * @return void
     */
    function incrementTurnCounter()
    {
        $turnCounter = self::getGameStateValue(TURN_COUNTER);
        $turnCounter += 1;
        self::setGameStateValue(TURN_COUNTER, $turnCounter);
    }


    /***********************************************************************************************
     *    PLAYER ACTIONS::Methods when players trigger actions                                      *
     ************************************************************************************************/

    /**
     * Change the face of a die from BLANK to GHOST or vice versa
     * @param int $dieId Id of die to change
     * @return void
     */
    function changeDie($dieId)
    {
        // Change die to opposite value and flag room power as resolved
        $die = $this->diceManager->changeDieFace($dieId);
        $this->roomManager->setResolvedRoomAbility(DGIT_TRUE);

        self::notifyAllPlayers(
            CHANGE_DIE,
            clienttranslate('${player_name} changes a die to a ${face} face'),
            array(
                'player_name' => $this->getActivePlayerName(),
                'face' => $die->getFace() == BLANK ? clienttranslate('Blank') : clienttranslate('Ghost'),
                'die' => $die->getUiData(),
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Dispel a full set of cards for the current player
     * This should only be trigger after a player selects a card type after using the Tome
     * @param int $cardType The type of card to dispel
     * @return void
     */
    function dispelSet($cardType)
    {
        $player = $this->playerManager->getPlayer();

        // Get all of a players cards of the type
        $cards = $this->cardManager->getPlayerCardsOfType($player->getId(), $cardType);
        // Dispel them
        $this->cardManager->moveCards($cards, DISPELED);
        // Adjust player dispel value
        $this->playerManager->adjustPlayerDispeled($player->getId(), count($cards));

        // Determine total curse value of cards
        $totalCurseValue = 0;
        $cardName = '';
        $statName = '';
        foreach($cards as $card)
        {
            $totalCurseValue += $card->getCurses();
            $cardName = $card->getName();
            $statName = $card->getStatName();
        }
        // Adjust player curse total
        $this->playerManager->adjustPlayerCurses($player->getId(), $totalCurseValue * -1);
        self::incStat($totalCurseValue * -1, $statName . '_curses', $player->getId());
        self::incStat(count($cards), $statName . '_dispeled', $player->getId());

        self::notifyAllPlayers(
            DISPEL_CARDS,    
            clienttranslate('${player_name} dispels ${amount} ${cardName} ${plural} worth a total of ${curses} Curses'),
            array(
                'player_name' => self::getActivePlayerName(),
                'amount' => count($cards),
                'cardName' => $cardName,
                'plural' => count($cards) == 1 ? clienttranslate('card') : clienttranslate('cards'),
                'curses' => $totalCurseValue,
                'curseTotal' => $totalCurseValue * -1,
                'player' => $player->getUiData(),
                'cards' => $this->cardManager->getUiDataFromCards($cards),
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Place a meeple of the active player in the chosen room space
     * @param int $roomPosition UI position of target room
     * @param int $space Chosen space in the room
     * @return void
     */
    function placeMeeple($roomPosition, $space)
    {
        $player = $this->playerManager->getPlayer(self::getActivePlayerId());

        // Get the room object
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomPosition);
        // Move the meeple from player's hand to chosen space 
        $meeple = $this->meepleManager->moveMeepleToRoom($player, $roomPosition, $space);
        // Get all the meeples that are now iun the room
        $meeplesInRoom = $this->meepleManager->getMeeples(ROOM_PREPEND . $room->getUiPosition());

        self::notifyAllPlayers(
            PLACE_MEEPLE,
            clienttranslate('${player_name} places a meeple in The ${roomName}'),
            array(
                'player_name' => $this->getActivePlayerName(),
                'roomName' => $room->getName(),
                'player' => $player->getUiData(),
                'room' => $room->getUiData(),
                'meeple' => $meeple->getUiData(),
            ));

        // If the room is the secret passage let the player look at the facedown card
        if ($room->getType() == SECRET_PASSAGE)
        {
            self::notifyAllPlayers(
                SECRET_PASSAGE_PEEK,
                clienttranslate('${player_name} can now see the hidden card in The Secret Passage'),
                array(
                    'player_name' => $this->getActivePlayerName(),
                )
            );
        }

        // If space 1 of attic player gains a ghost
        if ($room->getType() == ATTIC && $space == 1)
        {
            $this->playerManager->adjustPlayerGhosts($player->getId(), 1);
            self::notifyAllPlayers(
                ADJUST_GHOSTS,
                clienttranslate('${player_name} gains a Ghost from placing a meeple in The Attic'),
                array(
                    'player_name' => $this->getActivePlayerName(),
                    'playerId' => $player->getId(),
                    'amount' => 1,
                )
            );
        }

        // If space 4 of nursery player discards a ghost
        if ($room->getType() == NURSERY && $space == 4 && $player->getGhostTokens() > 0)
        {
            $this->playerManager->adjustPlayerGhosts($player->getId(), -1);
            self::notifyAllPlayers(
                ADJUST_GHOSTS,
                clienttranslate('${player_name} discards a Ghost from placing a meeple in The Nursery'),
                array(
                    'player_name' => $this->getActivePlayerName(),
                    'playerId' => $player->getId(),
                    'amount' => -1,
                )
            );
        }

        if (count($meeplesInRoom) == 3)
        {
            // If there are 3 meeples in the room we need to resolve it
            $this->roomManager->setRoomResolver(self::getActivePlayerId());
            $this->roomManager->setRoomResolving($room->getUiPosition());
            $this->gamestate->nextState(RESOLVE_ROOM);
        }
        else
        {
            // Otherwise, bump turn counter and go to next player
            self::incrementTurnCounter();
            $this->gamestate->nextState(NEXT_PLAYER);
        }
    }

    /**
     * Player inititated dice roll
     * Triggered from activating the basement
     * @return void
     */
    function rollDice()
    {
        $roomResolving = $this->roomManager->getRoomResolving();
        $this->roomManager->setResolvedRoomAbility(DGIT_TRUE);

        self::notifyAllPlayers(
            'reroll',
            clienttranslate('${player_name} chooses to re-roll the dice'),
            array(
                'player_name' => self::getActivePlayerName(),
            )
        );

        // Count dice on cards and roll the dice
        $diceToRoll = $this->cardManager->countDiceIconsInRoom($roomResolving);
        $diceRolled = $this->diceManager->rollDice($diceToRoll);

        self::notifyAllPlayers(
            ROLL_DICE,    
            clienttranslate('${player_name} rolls ${ghostsRolled} ${plural} on ${diceToRoll} dice'),
            array(
                'player_name' => self::getActivePlayerName(),
                'ghostsRolled' => $this->diceManager->getGhostsRolled(),
                'plural' => $this->diceManager->getGhostsRolled() == 1 ? clienttranslate('Ghost') : clienttranslate('Ghosts'),
                'diceToRoll' => $diceToRoll,
                'diceRolled' => $diceRolled,
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Skip an optional action
     * Choice after basement or hallway activation
     * @return void
     */
    function skip()
    {
        $roomUiPosition = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomUiPosition);
        $this->roomManager->setResolvedRoomAbility(DGIT_TRUE);

        self::notifyAllPlayers(
            SKIP,
            clienttranslate('${player_name} ${skipText}'),
            array(
                'player_name' => $this->getActivePlayerName(),
                'skipText' => $room->getAbilitySkipText(),
            )
        );

        $this->gamestate->nextState(RESOLVE_ROOM);
    }

    /**
     * Player takes a card from a room
     * @param int $cardId Id of card being taken
     * @return void
     */
    function takeCard($cardId)
    {
        $player = $this->playerManager->getPlayer();
        $room = $this->roomManager->getFaceupRoomByUiPosition($this->roomManager->getRoomResolving());
        $cardPosition = $this->cardManager->getCursedCardById($cardId)->getUiPosition();

        // Move card to player's tabeluau
        $card = $this->cardManager->takeCardFromRoom($cardId, $player);
        // Player gains curses from card
        $this->playerManager->adjustPlayerCurses($player->getId(), $card->getCurses());
        // Move meeple back to hand
        $meeple = $this->meepleManager->triggerMeeple($player->getId(), $room->getUiPosition());
        self::incStat($card->getCurses(), $card->getStatName() . '_curses', $player->getId());
        self::incStat(1, $card->getStatName() . '_taken', $player->getId());

        self::notifyAllPlayers(
            TAKE_CARD,    
            clienttranslate('${player_name} takes the ${cardName} and collects ${amount} ${plural}'),
            array(
                'player_name' => self::getActivePlayerName(),
                'cardName' => $card->getName(),
                'amount' => $card->getCurses(),
                'plural' => $card->getCurses() == 1 ? 'Curse' : 'Curses',
                'player' => $player->getUiData(),
                'card' => $card->getUiData(),
            )
        );

        self::notifyAllPlayers(
            RETURN_MEEPLE,
            '',
            array(
                'meeple' => $meeple->getUiData(),
            )
        );

        if($room->getType() == LIBRARY) {
            if($cardPosition == 1) {
                // If player takes the card in space 1 of the library they gain a ghost
                $this->playerManager->adjustPlayerGhosts($player->getId(), 1);
                self::notifyAllPlayers(
                    ADJUST_GHOSTS,
                    clienttranslate('${player_name} gains a Ghost from taking the first card in The Library'),
                    array(
                        'player_name' => $this->getActivePlayerName(),
                        'playerId' => $player->getId(),
                        'amount' => 1,
                    )
                );
            }
            if($cardPosition == 3 && $player->getGhostTokens() > 0) {
                // If player takes the card in space 3 of the library they discard a ghost
                $this->playerManager->adjustPlayerGhosts($player->getId(), -1);
                self::notifyAllPlayers(
                    ADJUST_GHOSTS,
                    clienttranslate('${player_name} discards a Ghost from taking the third card in The Library'),
                    array(
                        'player_name' => $this->getActivePlayerName(),
                        'playerId' => $player->getId(),
                        'amount' => -1,
                    )
                );
            }
        }

        // Persist the id of the card chosen for use in the triggering card effects state
        $this->cardManager->setLastSelectedCard($cardId);
        $this->gamestate->nextState(TRIGGER_CARD_EFFECT);
    }


    /***********************************************************************************************
     *    GAME STATE ARGUMENTS::Methods to pass arguments required for a game state                 *
     ************************************************************************************************/

    /**
     * Args for room ability state
     * @return array Array of args
     */
    function argsRoomAbility()
    {
        $roomUiPosition = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomUiPosition);

        return array(
            'ability' => $room->getAbilityText(),
            'dice' => $this->diceManager->getUiData(),
            'room' => $room->getUiData(),
        );
    }

    /**
     * Args for select card state
     * @return array Array of args
     */
    function argsSelectCard()
    {
        $roomUiPosition = $this->roomManager->getRoomResolving();

        return array(
            'roomResolving' => $roomUiPosition,
        );
    }

    /**
     * Args for trigger card effect state
     * @return array Array of args
     */
    function argsTriggerCardEffect()
    {
        $card = $this->cardManager->getLastSelectedCard();
        return array(
            'ability' => $card->getAbilityText(),
            'card' => $card->getUiData(),
        );
    }


    /***********************************************************************************************
     *    GAME STATE::Global game state actions                                                     *
     ************************************************************************************************/

    // The player(s) with the most ghosts will gain curses
    function stGameEndCheckGhosts()
    {
        $this->playerManager->handleGameEndGhosts();
        $winningPlayers = $this->playerManager->getWinningPlayers();

        self::notifyAllPlayers(
            REVEAL_WINNERS,
            '',
            array('winningPlayers', $this->playerManager->getUiDataFromPlayers($winningPlayers))
        );

        $players = $this->playerManager->getPlayers();
        $playerCount = count($players);
        $totalCurses = 0;
        $totalGhosts = 0;
        $totalCardsDispeled = 0;
        $totalCursesDispeled = 0;
        foreach($players as $player) {
            $totalCurses = $totalCurses + $player->getCurses();
            $totalGhosts = $totalGhosts + $player->getGhostTokens();
            $totalCardsDispeled = $totalCardsDispeled + $player->getCardsDispeled();
            $totalCursesDispeled = $totalCursesDispeled + self::getStat('curses_dispeled', $player->getId());
        }

        self::setStat($totalCurses / $playerCount, 'avg_curses');
        self::setStat($totalGhosts / $playerCount, 'avg_ghosts');
        self::setStat($totalCardsDispeled / $playerCount, 'avg_cards_dispeled');
        self::setStat($totalCursesDispeled / $playerCount, 'avg_curses_dispeled');

        $this->gamestate->nextState(GAME_END);
    }

    /**
     * Handle transition to next player
     * @return void
     */
    function stNextPlayer()
    {
        if(self::getGameStateValue(TURN_COUNTER) == self::getGameStateValue(TOTAL_TURNS)) {
            // All cards are taken, trigger end game powers on cards
            $this->gamestate->nextState(TRIGGER_GAME_END_CARD_EFFECTS);
        } else {
            // If not just move the next player
            $nextPlayer = $this->activeNextPlayer();

            self::notifyAllPlayers(
                CHANGE_PLAYER,
                clienttranslate('${player_name} is now the active player'),
                array(
                    'player_name' => self::getActivePlayerName(),
                    'nextPlayer' => $nextPlayer,
                )
            );

            $this->giveExtraTime(self::getActivePlayerId());

            $this->gamestate->nextState(PLAYER_TURN);
        }
    }

    /**
     * Handle transitions in resolve room phase
     * @return void
     */
    function stResolveRoom()
    {
        $roomResolving = $this->roomManager->getRoomResolving();
        $room = $this->roomManager->getFaceupRoomByUiPosition($roomResolving);

        // If room is Secret Passage reveal hidden card if it hasn't already been revealed
        if ($room->getType() == SECRET_PASSAGE && $this->roomManager->getSecretPassageRevealed() == DGIT_FALSE)
        {
            $this->roomManager->setSecretPassageRevealed(DGIT_TRUE);
            self::notifyAllPlayers(
                SECRET_PASSAGE_REVEAL,
                clienttranslate('All players can now see the hidden card in The Secret Passage'),
                array()
            );
        }

        // Roll dice if they haven't been rolled yet
        if ($this->diceManager->getGhostsRolled() < 0)
        {
            $diceToRoll = $this->cardManager->countDiceIconsInRoom($roomResolving);
            $diceRolled = $this->diceManager->rollDice($diceToRoll);

            self::notifyAllPlayers(
                ROLL_DICE,    
                clienttranslate('${player_name} rolls ${ghostsRolled} ${plural} on ${diceToRoll} dice'),
                array(
                    'player_name' => self::getActivePlayerName(),
                    'ghostsRolled' => $this->diceManager->getGhostsRolled(),
                    'plural' => $this->diceManager->getGhostsRolled() == 1 ? clienttranslate('Ghost') : clienttranslate('Ghosts'),
                    'diceToRoll' => $diceToRoll,
                    'diceRolled' => $diceRolled,
                )
            );
        }

        // If room has an unresolved ability we need to handle it
        if ($room->hasResolveAbility() && $this->roomManager->getResolvedRoomAbility() == DGIT_FALSE)
        {
            $this->gamestate->nextState(ROOM_RESOLUTION_ABILITY);
        }
        else
        {
            // If we have not resolved ghosts yet we need to do that
            if($this->roomManager->getResolvedRoomGhosts() == DGIT_FALSE) {
                $this->roomManager->resolveRoomGhosts($roomResolving);
            }
            // Get next meeple in room
            $nextMeeple = $this->meepleManager->getTopMeepleInRoom($roomResolving);

            if ($nextMeeple)
            {
                $cards = $this->cardManager->getCursedCards(ROOM_PREPEND . $roomResolving);
                $this->gamestate->changeActivePlayer($nextMeeple->getOwner());
                if (sizeof($cards) == 1) {
                    $card = reset($cards);
                    $this->takeCard($card->getId());

                } else {
                    // Get the next player to act
                    $this->gamestate->nextState(SELECT_CARD);
                }
            }
            else
            {
                // Else all meeples have been resolved and we need to clean up the room

                // Reset Dice
                $this->diceManager->resetDice();
                self::notifyAllPlayers(
                    RESET_DICE,
                    clienttranslate('Resetting dice'),
                    array(
                        'dice' => $this->diceManager->getUiData(),
                    )
                );

                // Draw new room and cards if there are enough cards left
                if($this->cardManager->countCursedCards(DECK) >= 3) {
                    // Flip the room to its opposite side and reset resolve abilities
                    $currentRoom = $this->roomManager->getFaceupRoomByUiPosition($roomResolving);
                    if($currentRoom->hasResolveAbility()) {
                        $this->roomManager->setResolvedRoomAbility(DGIT_FALSE);
                    }
                    if($currentRoom->getType() == SECRET_PASSAGE) {
                        $this->roomManager->setSecretPassageRevealed(DGIT_FALSE);
                    }
                    $newRoom = $this->roomManager->flipRoom($roomResolving);
                    self::notifyAllPlayers(
                        FLIP_ROOM, 
                        clienttranslate('The ${currentName} flips over to The ${newName}'),
                        array(
                            'currentName' => $currentRoom->getName(),
                            'newName' => $newRoom->getName(),
                            'currentRoom' => $currentRoom->getUiData(),
                            'newRoom' => $newRoom->getUiData(),
                        )
                    );

                    // Draw New Cards
                    $this->cardManager->drawNewCardsForRoom($newRoom);
                    self::notifyAllPlayers(
                        NEW_CARDS,
                        clienttranslate('Three new cards drawn for The ${roomName}'),
                        array(
                            'roomName' => $newRoom->getName(),
                            'room' => $newRoom->getUiData(),
                            'cards' => $this->cardManager->getUiData(ROOM_PREPEND.$newRoom->getUiPosition()),
                        )
                    );
                } else {
                    // All the cards are gone, so just remove the room tile from the game
                    $currentRoom = $this->roomManager->getFaceupRoomByUiPosition($roomResolving);
                    if($currentRoom->hasResolveAbility()) {
                        $this->roomManager->setResolvedRoomAbility(DGIT_FALSE);
                    }
                    if($currentRoom->getType() == SECRET_PASSAGE) {
                        $this->roomManager->setSecretPassageRevealed(DGIT_FALSE);
                    }
                    $this->roomManager->flipRoomFacedown($currentRoom);
                    self::notifyAllPlayers(
                        FLIP_ROOM_FACEDOWN,
                        '',
                        array(
                            'room' => $currentRoom->getUiData(),
                        )
                    );
                }
                
                // Change back to active player, reset resolution globals, go to next state
                self::incrementTurnCounter();
                $this->gamestate->changeActivePlayer($this->roomManager->getRoomResolver());
                $this->roomManager->setRoomResolver(0);
                $this->roomManager->setRoomResolving(0);
                $this->roomManager->setResolvedRoomGhosts(DGIT_FALSE);
                $this->gamestate->nextState(NEXT_PLAYER);
            }
        }
    }

    /**
     * Check if the active player should do something in this phase.
     * This will only be if the effect of the Tome needs to be triggered.
     * If card has in game ability trigger it and move to next phase
     * @return void
     */
    function stTriggerCardEffect()
    {
        $args = [];
        $player = $this->playerManager->getPlayer();
        $card = $this->cardManager->getLastSelectedCard();
        $args['player'] = $player;
        $args['selectedCard'] = $card;

        // If card is end game trigger, ignore it
        if($card->isEndGameTrigger()) {
            $this->gamestate->nextState(RESOLVE_ROOM);
        } else {
            // If card is a tome check if the effect should be triggered
            if($card->getType() == TOME && !$this->cardManager->triggerTome($player->getId())) {
                $this->gamestate->nextState(RESOLVE_ROOM);
            }
            // Handle card types without choices
            if($card->getType() != TOME) {
                $card->triggerEffect($args);
                $this->gamestate->nextState(RESOLVE_ROOM);
            }
        }
    }

    /**
     * Trigger the effects of all the end game cards
     * @return void
     */
    function stTriggerGameEndCardEffects()
    {
        $this->cardManager->triggerEndGameEffects();
        $this->gamestate->nextState(GAME_END_CHECK_GHOSTS);
    }


    /***********************************************************************************************
    *    ZOMBIE::Functions to handle players in a zombie state                                     *
    ************************************************************************************************/

    /**
     * called each time it is the turn of a player who has quit the game (= "zombie" player)
     * @param object $state A game state object
     * @param int $active_player Database ID of a player
     * @throws feException 
     * @return void
     */
    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive( $active_player, '' );
            
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }

    
    /***********************************************************************************************
    *    DB UPGRADE::Functions to handle games with old DB schema                                  *
    ************************************************************************************************/

    /**
     * If database schema changes, apply changes to currently running games with old schema
     * @param int $from_version The version of the schema in the current game
     * @return void
     */
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            // ! important ! Use DBPREFIX_<table_name> for all tables
//
//            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
//            self::applyDbUpgradeToAllDB( $sql );
//        }
//        // Please add your future database scheme changes here
//
//
    }    
}
