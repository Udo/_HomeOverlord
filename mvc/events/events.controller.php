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
    foreach(H2Configuration::getMenuLinks() as $item)
      $submenu[] = '<a href="'.$item['url'].'">'.$item['title'].'</a>';
    
    return('<div class="submenu2">'.implode(' | ', $submenu).'</div>');
  }

}
