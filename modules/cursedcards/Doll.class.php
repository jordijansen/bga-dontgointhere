<?php

/**
 * ------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * Doll.class.php
 * 
 * Doll card object
 */

class Doll extends DontGoInThereCursedCard
{
    public function __construct($game, $row)
    {
        parent::__construct($game);
        $this->id = $row[ID];
        $this->name = clienttranslate('Doll');
        $this->type = DOLL;
        $this->cssClass = "dgit-card-doll-".$row[TYPE_ARG];
        $this->tooltipText = self::buildTooltipText();
        $this->curses = $row[TYPE_ARG];
        $this->diceIcons = self::determineDiceIcons($row[TYPE_ARG]);
        $this->endGameTrigger = false;
        $this->uiPosition = $row[LOCATION_ARG];
    }

    /**
     * Build tooltip text for Doll
     * @return string Tooltip text
     */
    private function buildTooltipText()
    {
        return clienttranslate('When you have a set of Doll cards whose Curse values add up to exactly 6, immediatelty dispel those Doll cards.');
    }

    /**
     * If a player had Doll cards that equal exactly 6, dispel those cards
     * @param Array $args for effect
     * @return void
     */
    public function triggerEffect($args)
    {
        $player = $args['player'];
        $selectedCard = $args['selectedCard'];
        // Get doll cards the player has
        $dollCards = $this->game->cardManager->getPlayerCardsOfType($player->getId(), DOLL);
        
        // Find a valid set of doll cards
        $validSet = false;
        switch($selectedCard->getCurses()) {
            case 1:
                $validSet = self::getValidSetForOne($selectedCard, $dollCards);
                break;
            case 2:
                $validSet = self::getValidSetForTwo($selectedCard, $dollCards);
                break;
            case 3:
                $validSet = self::getValidSetForThree($selectedCard, $dollCards);
                break;
            case 4:
                $validSet = self::getValidSetForFour($selectedCard, $dollCards);
                break;
            default:
                break;
        }

        // If a valid set exists move those cards dispeled
        if($validSet != false) {
            $this->game->playerManager->adjustPlayerDispeled($player->getId(), count($validSet));
            $this->game->cardManager->moveCards($validSet, DISPELED);
            $this->game->notifyAllPlayers(
                TRIGGER_DOLL,    
                clienttranslate('${player_name} dispels ${amount} Doll cards '),
                array(
                    'player_name' => $this->game->getActivePlayerName(),
                    'amount' => count($validSet),
                    'player' => $player->getUiData(),
                    'cards' => $this->game->cardManager->getUiDataFromCards($validSet),
                )
            );
        }
    }

    /**
     * Find a valid set if the player took a one
     * @param DontGoInThereCursedCard $oneCard The card the player selected
     * @param array<DontGoInThereCursedCard> $dollCards array of doll cards the player has
     * @return array|bool array of cards in valid set or false if no valid set
     */
    private function getValidSetForOne($oneCard, $dollCards) 
    {
        $cardList = [];
        $cardList[] = $oneCard;

        $anotherOneCard = self::dollsContainCard($dollCards, 1, $oneCard->getId());
        $twoCard = self::dollsContainCard($dollCards, 2, null);
        $threeCard = self::dollsContainCard($dollCards, 3, null);
        $fourCard = self::dollsContainCard($dollCards, 4, null);

        if($twoCard != false && $threeCard != false) {
            $cardList[] = $twoCard;
            $cardList[] = $threeCard;
            return $cardList;
        }

        if($anotherOneCard != false && $fourCard != false) {
            $cardList[] = $anotherOneCard;
            $cardList[] = $fourCard;
            return $cardList;
        }

        if($anotherOneCard != false && $twoCard != false) {
            $anotherTwoCard = self::dollsContainCard($dollCards, 2, $twoCard->getId());
            if($anotherTwoCard != false) {
                $cardList[] = $anotherOneCard;
                $cardList[] = $twoCard;
                $cardList[] = $anotherTwoCard; 
            }
        }

        return false;

    }

    /**
     * Find a valid set if the player took a two
     * @param DontGoInThereCursedCard $twoCard The card the player selected
     * @param array<DontGoInThereCursedCard> $dollCards array of doll cards the player has
     * @return array|bool array of cards in valid set or false if no valid set
     */
    private function getValidSetForTwo($twoCard, $dollCards) 
    {
        $cardList = [];
        $cardList[] = $twoCard;

        $oneCard = self::dollsContainCard($dollCards, 1, null);
        $anotherTwoCard = self::dollsContainCard($dollCards, 2, $twoCard->getId());
        $threeCard = self::dollsContainCard($dollCards, 3, null);
        $fourCard = self::dollsContainCard($dollCards, 4, null);

        if($fourCard != false) {
            $cardList[] = $fourCard;
            return $cardList;
        }

        if($oneCard != false && $threeCard != false) {
            $cardList[] = $oneCard;
            $cardList[] = $threeCard;
            return $cardList;
        }

        if($anotherTwoCard != null && $oneCard != null) {
            $anotherOneCard = self::dollsContainCard($dollCards, 1, $oneCard->getId());
            if($anotherOneCard != false) {
                $cardList[] = $anotherTwoCard;
                $cardList[] = $oneCard;
                $cardList[] = $anotherOneCard;
                return $cardList;
            }
        }

        return false;
    }

    /**
     * Find a valid set if the player took a three
     * @param DontGoInThereCursedCard $threeCard The card the player selected
     * @param array<DontGoInThereCursedCard> $dollCards array of doll cards the player has
     * @return array|bool array of cards in valid set or false if no valid set
     */
    private function getValidSetForThree($threeCard, $dollCards)
    {
        $cardList = [];
        $cardList[] = $threeCard;

        $oneCard = self::dollsContainCard($dollCards, 1, null);
        $twoCard = self::dollsContainCard($dollCards, 2, null);
        $anotherThreeCard = self::dollsContainCard($dollCards, 3, $threeCard->getId());

        if($oneCard != false && $twoCard != false) {
            $cardList[] = $oneCard;
            $cardList[] = $twoCard;
            return $cardList;
        }

        if($anotherThreeCard != false){
            $cardList[] = $anotherThreeCard;
            return $cardList;
        }

        return false;
    }

    /**
     * Find a valid set if the player took a four
     * @param DontGoInThereCursedCard $fourCard The card the player selected
     * @param array<DontGoInThereCursedCard> $dollCards array of doll cards the player has
     * @return array|bool array of cards in valid set or false if no valid set
     */
    private function getValidSetForFour($fourCard, $dollCards) 
    {
        $cardList = [];
        $cardList[] = $fourCard;

        $oneCard = self::dollsContainCard($dollCards, 1, null);
        $twoCard = self::dollsContainCard($dollCards, 2, null);

        if($twoCard != false) {
            $cardList[] = $twoCard;
            return $cardList;
        }

        if($oneCard != false) {
            $anotherOneCard = self::dollsContainCard($dollCards, 1, $oneCard->getId());
            if($anotherOneCard != false) {
                $cardList[] = $oneCard;
                $cardList[] = $anotherOneCard;
                return $cardList;
            }
        }

        return false;
    }

    /**
     * Check if doll is in list
     * @param array<DontGoInThereCursedCard> $dollCards cards in a list
     * @param int $curseValue The value of card to find
     * @param mixed $existingId The id of a previously found card so we don't find it again
     * @return mixed
     */
    private function dollsContainCard($dollCards, $curseValue, $existingId)
    {
        foreach($dollCards as $dollCard)
        {
            if($dollCard->getId() != $existingId && $dollCard->getCurses() == $curseValue) {
                return $dollCard;
            }
        }
        return false;
    }
}