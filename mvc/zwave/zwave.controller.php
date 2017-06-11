<?php
  
class ZWaveController extends H2Controller
{
  
  function __init()
  {
    $this->access('local,internal,auth');
    $GLOBALS['submenu'] = array();
  }

}