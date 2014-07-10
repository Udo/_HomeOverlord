<?php

function rev($a, $b)
{
  if($GLOBALS['reverse_action'])
    return($b);
  else
    return($a);
}

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
  
  function execTriggerScript($code, $data, $noExec = true)
  {
    $GLOBALS['command-mode'] = 'trigger';
    $ds = $data['ds'];
    if(isset($ds))
      $GLOBALS['command-source'] = '#'.$ds['e_key'].' '.first($data['emitter_name'], $data['event']);
    foreach($data as $k => $v) $$k = $v;
    $code = trim($code);
    ob_start();
    if(substr($code, 0, 1) == ':' || substr($code, 0, 1) == '>')
    {
      $lines = explode(chr(10), $code);
      foreach($lines as $line)
      {
        $line = trim($line);
        if(substr($code, 0, 1) == ':')
        {
          $seg = explode(':', substr($line, 1));
          if($seg[0] == 'HAL') 
          {
            $dev = new H2HALDevice($seg[1]);
            $dval = rev($seg[2], $seg[3]);
            $dev->state($dval, $GLOBALS['command-source']);
          }
          else 
          {
            $dval = rev($seg[2], $seg[3]);
            if(trim($seg[0]) != '' && sizeof($seg) > 2 && $dval != '' && $dval != '-')
            {
              deviceCommand($seg[0], $seg[1], $dval);
              print($seg[0].'='.$dval.', ');
            }
          }
        }
        else if(substr($code, 0, 1) == '>')
        {
          $seg = explode(':', substr($line, 1));
          switch($seg[0])
          {
            case('>TIMER'): 
            { 
              $tevt = array(
                'minutes' => $seg[1],
                );
              if($seg[2] == 'deviceCommand')
              {
                CutSegment(':deviceCommand:', $line);
                $tevt['deviceCommand'] = $line;
              }
              else
              {
                $tevt['event'] = $seg[2];
              }
              sendToNode('timedevent', $tevt);
              break;
            }
          }        
        }
      }
    }
    else if(!$noExec)
    {
      eval($code);
    }
    return(ob_get_clean());    
  }

  function ajax_tick()
  {
    $this->skipView = false;
  }
  
  function weather()
  {
  
  }

  function callEventHandlers($handlers, $data, $onlyCType = true)
  {
    if($onlyCType)
      $cwhere = ' e_type="C" AND ';
    foreach(array('', '_rev') as $addressType)
    {
      $GLOBALS['reverse_action'] = $addressType == '_rev';
      $where = array();
      foreach($handlers as $h)
        $where[] = 'e_address'.$addressType.' = ?';
      foreach(o(db)->get('SELECT * FROM events
        WHERE '.$cwhere.' ('.implode(' OR ', $where).')
        ORDER BY e_order ASC', $handlers) as $eds)
      {
        $this->ignoreExecution = false;
        $this->execTriggerScript($eds['e_code'], array(
          'emitter_id' => $data['device'],
          'emitter_param' => $data['param'],
          'emitter_value' => $data['value'],
          'emitter_name' => first($data['ds']['d_name'], $data['ds']['d_id']),
          'ds' => &$eds,
          'event' => $eds['e_address'.$addressType],
          ));
        if(!$this->ignoreExecution)
          o(db)->query('UPDATE events SET e_lastcalled = '.time().' WHERE e_key = '.$eds['e_key']);
      }
    }
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
}