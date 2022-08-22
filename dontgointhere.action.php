<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DontGoInThere implementation : © Evan Pulgino <evan.pulgino@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * dontgointhere.action.php
 *
 * DontGoInThere main action entry point
 *
 */
  
class action_dontgointhere extends APP_GameAction
{ 
  // Constructor: please do not modify
  public function __default()
  {
    if( self::isArg( 'notifwindow') ) {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
    } else {
      $this->view = "dontgointhere_dontgointhere";
      self::trace( "Complete reinitialization of board game" );
    }
  }

  public function placeMeeple()
  {
    self::setAjaxMode();

    $room = self::getArg("room", AT_posint, true);
    $space = self::getArg("space", AT_posint, true);

    $this->game->placeMeeple($room, $space);

    self::ajaxResponse();
  }

}
  

