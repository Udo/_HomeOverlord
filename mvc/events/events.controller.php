<?php

class EventsController extends H2Controller
{
  function __init()
  {
    $this->access('local,internal,auth');
  }

  function index()
  {

  }

  function manual()
  {
  
  }
  
  function _getSubmenu2()
  {
    foreach(array(
      'index', 'manual',
      ) as $act)
      $submenu[] = '<a style="'.($_REQUEST['action'] == $act ? 'font-weight:bold;' : '').'" href="'.
        actionUrl($act).
        '">'.l10n($_REQUEST['controller'].'.'.$act).'</a>';
    
    return('<div class="submenu2">'.implode(' | ', $submenu).'</div>');
  }

}
