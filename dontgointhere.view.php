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
 * dontgointhere.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in dontgointhere_dontgointhere.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
require_once( APP_BASE_PATH."view/common/game.view.php" );
  
 class view_dontgointhere_dontgointhere extends game_view
 {
   function getGameName() {
    return "dontgointhere";
  }    
  function build_page( $viewArgs )
  {		
    // Template name
    $template = self::getGameName().'_'.self::getGameName();

    // Inflate players
    $players = $this->game->playerManager->getPlayersInViewOrder();
    $this->page->begin_block($template, 'playerscorerow');
    $this->page->begin_block($template, 'playercardtype');
    $this->page->begin_block($template, 'playerarea');
    foreach($players as $playerKey => $player)
    {
      $this->page->reset_subblocks('playercardtype');

      $this->page->insert_block(
        'playerscorerow',
        array(
          'PLAYER_ID' => $player->getId(),
          'PLAYER_NAME' => $player->getName(),
          'PLAYER_COLOR' => $player->getColor(),
          'PLAYER_GHOSTS' => $player->getGhostTokens(),
          'PLAYER_CURSES' => $player->getCurses(),
          'PLAYER_NATURAL_ORDER' => $player->getNaturalOrder(),
        )
      );

      for ($cardType = AMULET; $cardType <= TWIN; $cardType++)
      {
        $this->page->insert_block(
          'playercardtype',
          array(
            'PLAYER_ID' => $player->getId(),
            'CARD_TYPE' => $cardType,
          )
        );
      }

      $this->page->insert_block(
        'playerarea',
        array(
          'PLAYER_ID' => $player->getId(),
          'PLAYER_NAME' => $player->getName(),
          'PLAYER_COLOR' => $player->getColor(),
          'PLAYER_NATURAL_ORDER' => $player->getNaturalOrder(),
        )
      );
    }

    // Inflate a pile of ghosts
    $this->page->begin_block($template, 'ghost');
    for($ghostNumber = 1; $ghostNumber <= 6; $ghostNumber++)
    {
      $this->page->insert_block(
        'ghost',
        array(
          'GHOST_NUM' => $ghostNumber,
          'GHOST_TYPE' => ceil($ghostNumber / 2),
          'DELAY' => ($ghostNumber - 1) * 100,
          'X_TIME' => bga_rand(10, 20),
          'Y_TIME' => bga_rand(10, 20),
          'SPIN_TIME' => bga_rand(10, 20),
          'Z_INDEX' => bga_rand(1, 70),
        )
      );
    }

    // Inflate dice
    $this->page->begin_block($template, 'die');
    for ($dieNumber = 1; $dieNumber <= 6; $dieNumber++)
    {
      $this->page->insert_block(
        'die',
        array(
          'DIE_NUM' => $dieNumber,
        )
      );
    }

    // Inflate rooms
    $this->page->begin_block($template, 'roomspace');
    $this->page->begin_block($template, 'room');
    for($roomNumber = 1; $roomNumber <= 3; $roomNumber++)
    {
      $this->page->reset_subblocks('roomspace');

      for ($spaceNumber = 1; $spaceNumber <= 4; $spaceNumber++)
      {
        $this->page->insert_block(
          'roomspace',
          array(
            'ROOM_NUM' => $roomNumber,
            'SPACE_NUM' => $spaceNumber,
          )
        );
      }
      $this->page->insert_block(
        'room',
        array(
          'ROOM_NUM' => $roomNumber,
        )
      );
    }

    // Populate variables
    $this->tpl['CHANGE'] = self::_("Change");
    $this->tpl['DISPEL'] = self::_("Dispel");
    $this->tpl['ROLL'] = self::_("Roll");

    /*********** Do not change anything below this line  ************/
  }
}
  

