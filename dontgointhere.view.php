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
  	  // Get players & players number
      $players = $this->game->loadPlayersBasicInfos();
      $players_nbr = count( $players );

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


      // Inflate rooms w/ cards blocks
      $this->page->begin_block($template, 'roomcard');
      $this->page->begin_block($template, 'room');
      for($roomNumber = 1; $roomNumber <= 3; $roomNumber++)
      {
        $this->page->reset_subblocks('roomcard');

        for($cardNumber = 1; $cardNumber <=3; $cardNumber++)
        {
          $this->page->insert_block(
            'roomcard',
            array(
              'ROOM_NUM' => $roomNumber,
              'CARD_NUM' => $cardNumber,
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

      /*********** Do not change anything below this line  ************/
  	}
  }
  

