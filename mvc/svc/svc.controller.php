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
  
  function execTriggerScript($code, $data)
  {
    $ds = $data['ds'];
    if(isset($ds))
      $GLOBALS['command-source'] = '#'.$ds['e_key'].' '.first($data['emitter_name'], $data['event']);
    foreach($data as $k => $v) $$k = $v;
    ob_start();
    if(substr($code, 0, 1) == ':')
    {
      $lines = explode(chr(10), $code);
      foreach($lines as $line)
      {
        $line = trim($line);
        if($line != '')
        {
          $seg = explode(':', substr($line, 1));
          if(trim($seg[0]) != '' && sizeof($seg) > 2)
          {
            deviceCommand($seg[0], $seg[1], rev($seg[2], $seg[3]));
            print($seg[0].'='.rev($seg[2], $seg[3]).', ');
          }
        }
      }
    }
    else
    {
      eval($code);
    }
    return(ob_get_clean());    
  }

  function ajax_tick()
  {
    $weather = json_decode(file_get_contents('data/openweather.json'), true);
    @$mainWeather = $weather['weather'][0];
    
    $weekDay = date('N');
    $WE = $weekDay == 6 || $weekDay == 7 ? 'WEEKEND' : 'WEEKDAY';
    $weekDayName = $GLOBALS['weekdays'][$weekDay];
    $coolDown = time()-120;
    $eventAdresses = array('TICK');
    
    foreach(array(time(), time()-60) as $t)
    {
       foreach(array(
        'TIME-'.date('H:i', $t),
        $weekDayName.'-'.date('H:i', $t),
        $WE.'-'.date('H:i', $t),
        'MINUTE-'.date('i', $t),
        ) as $e) 
        $eventAdresses[] = $e;
        
      $sunRiseIn = round(($t - strtotime(date('H:i', getSunrise(false))))/60);
      $sunSetIn = round(($t - strtotime(date('H:i', getSunset(false))))/60);
      
      if($sunRiseIn > -600 && $sunRiseIn <= 0)
        $eventAdresses[] = 'SUNRISE'.$sunRiseIn;
      if($sunRiseIn < 600 && $sunRiseIn >= 0)
        $eventAdresses[] = 'SUNRISE+'.$sunRiseIn;
      if($sunSetIn > -600 && $sunSetIn <= 0)
        $eventAdresses[] = 'SUNSET'.$sunSetIn;
      if($sunSetIn < 600 && $sunSetIn >= 0)
        $eventAdresses[] = 'SUNSET+'.$sunSetIn;
      if($sunRiseIn == 0)
        $eventAdresses[] = 'SUNRISE';
      if($sunSetIn == 0)
        $eventAdresses[] = 'SUNSET';
    }
    
    $isDay = $t >= strtotime(date('H:i', getSunrise(false))) && $t <= strtotime(date('H:i', getSunset(false)));
    $dayString = $isDay ? 'DAY' : 'NIGHT';
      
    if(
      ($mainWeather['id'] >= 200 && $mainWeather['id'] < 250) || 
      ($mainWeather['id'] >= 803 && $mainWeather['id'] < 850) || 
      ($mainWeather['id'] >= 501 && $mainWeather['id'] < 550))
      $eventAdresses[] = $dayString.'-DARK';

    $eventAdresses[] = $dayString.'-'.strtoupper($mainWeather['main']);
    $eventAdresses[] = 'TICK-'.strtoupper($mainWeather['main']);
    
    // --------
    foreach(array('', '_rev') as $addressType)
    {
      $GLOBALS['reverse_action'] = $addressType == '_rev';
      $where = array();
      foreach($eventAdresses as $e)
        $where[] = '(e_address'.$addressType.' = ?)';
        
      $qry = 'SELECT * FROM events
        WHERE e_type="T" AND e_lastcalled < '.$coolDown.' AND ('.implode(' OR ', $where).')';
      $triggers = o(db)->get($qry, $eventAdresses);
  
      $report = array();
      foreach($triggers as $eds)
      {
        $this->ignoreExecution = false;
        $result = $this->execTriggerScript($eds['e_code'], array(
          'ds' => &$eds,
          'event' => $eds['e_address'.$addressType],
          ));
        $rline = 'triggered #'.$eds['e_key'].' '.$eds['e_address'.$addressType].' '.$result;
        $report[] = $rline;
        WriteToFile('log/tick.events.log', date('Y-m-d H:i:s').' '.$rline.chr(10));
        if(!$this->ignoreExecution)
          o(db)->query('UPDATE events SET e_lastcalled = '.time().' WHERE e_key = '.$eds['e_key']);
      }    
    }
        
    print(json_encode(array(
      'register' => $eventAdresses, 
      'report' => $report, 
      'profile' => $GLOBALS['profiler_log'])));
  }
  
  function weather()
  {
  
  }

  function callEventHandlers($handlers, $data)
  {
    foreach(array('', '_rev') as $addressType)
    {
      $GLOBALS['reverse_action'] = $addressType == '_rev';
      $where = array();
      foreach($handlers as $h)
        $where[] = 'e_address'.$addressType.' = ?';
      foreach(o(db)->get('SELECT * FROM events
        WHERE e_type="C" AND ('.implode(' OR ', $where).')
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
  
  function ajax_event()
  {
    $data = json_decode($_REQUEST['data'], true);
    $hdl = array(
        'all', 
        $data['type'],
        $data['type'].'-'.$data['device'],
        $data['type'].'-'.$data['param'],
        $data['type'].'-'.$data['device'].'-'.$data['param'],
        $data['type'].'-'.$data['device'].'-'.$data['param'].'-'.$data['value'],
        );
    if($data['param'] == 'PRESS_SHORT' || $data['param'] == 'PRESS_LONG_RELEASE')
      $hdl[] = $data['type'].'-'.$data['device'].'-PRESSED';

    $dds = o(db)->getDSMatch('devices', array(
      'd_bus' => $data['type'],
      'd_id' => $data['device'],
      ));
    
    $data['ds'] = $dds;
    
    $this->callEventHandlers(
      $hdl, 
      $data);
    WriteToFile('log/event.log', date('Y-m-d H:i:s').' '.$_REQUEST['data'].chr(10));
    
    $sds = array(
      'si_bus' => $data['type'],
      'si_name' => $data['device'],
      'si_param' => $data['param'],
      'si_value' => $data['value'],
      'si_time' => time(),
      'si_devicekey' => $dds['d_key'],
      'si_by' => 'BIDCOS',
      'si_mode' => 'RX',
      'si_ip' => first($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']),
      );
    o(db)->commit('stateinfo', $sds);
  }
}