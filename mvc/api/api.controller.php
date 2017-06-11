<?php
  
class APIController extends H2Controller
{
  
  function __init()
  {
    $this->access('any');
    $this->skipView = true;
    $GLOBALS['config']['page']['template'] = 'blank';
    $GLOBALS['submenu'] = array();
  }

  function index()
  {

  }
  
  function notify()
  {
    $eventName = strtoupper('API-'.$_REQUEST['type'].'-'.$_REQUEST['activity']);
    print($eventName);
    $evt = new H2Event();
    $evt->callHandlers(array($eventName), $_POST);
    print('<br/>'.implode('<br/>', $GLOBALS['log']));
    WriteToFile('log/api.notify.log', json_encode($_REQUEST).chr(10));
  }

}