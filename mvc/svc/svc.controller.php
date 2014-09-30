<?php



class SvcController extends H2Controller
{
  function __init()
  {
    $this->access('local,internal,auth');
    $GLOBALS['submenu'] = array();
  }

  function index()
  {

  }
  
  function ajax_tick()
  {
    $this->skipView = false;
    $this->access('local,auth');
  }
  
  function ajax_camscript() 
  {
    $this->skipView = false;
  }
  
  function ajax_timer() 
  {
    $this->skipView = false;
    $this->access('local,auth');
  }
  
  function weather()
  {
  
  }

  function callEventHandlers($handlers, $data, $onlyCType = true)
  {
    $evt = new H2Event();
    $evt->callHandlers($handlers, $data, $onlyCType);
  }
  
  function execTriggerScript($code, $data, $noExec = true)
  {
    $evt = new H2Event();
    $evt->executeScript($code, $data, $noExec);
    $this->ignoreExecution = $evt->ignoreExecution;
  }
  
  function ajax_getstate()
  {
    $r = array();
    foreach(o(db)->get('SELECT * FROM devices
      ORDER BY d_room, d_key') as $d)
    {
      $r['d'.$d['d_id']] = array('id' => $d['d_id'], 'state' => $d['d_state'], 'd_name' => $d['d_name']);    
    }    
    print(json_encode($r));
  }
  
  function ajax_notify()
  {
    WriteToFile('log/event.log', date('Y-m-d H:i:s').' notify: '.$_REQUEST['data'].chr(10));
    $data = json_decode($_REQUEST['data'], true);
    if($data['event'] != '')
    {
      $hdl = array(
          'all', 
          $data['event'],
          );
      $this->callEventHandlers(
        $hdl, 
        $data, 
        false);
    }
    if(isset($data['deviceCommand']))
    {
      WriteToFile('log/event.log', date('Y-m-d H:i:s').' deviceCommand: '.$data['deviceCommand'].chr(10));
      $this->execTriggerScript($data['deviceCommand'], $data);
    }
  }
  
  function ajax_event()
  {
    $this->skipView = false;
  }
  function ajax_nodesrv()
  {
    $this->skipView = false;
  }
}