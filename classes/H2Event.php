<?php

class H2Event 
{

  function handleDeviceLine($line, $data)
  {
    CutSegment(':', $line);
    $deviceId = CutSegment(':', $line);
    if($deviceId == 'HAL') 
    {
      $devId = CutSegment(':', $line);
      $value = CutSegment(':', $line); if($data['reverseAction']) $value = CutSegment(':', $line);
      $GLOBALS['log'][] = 'HAL '.$devId.'-'.$value;
      $dev = new H2HALDevice($devId);
      $dev->state($value, $GLOBALS['command-source']);
    }
    else 
    {
      $paramName = CutSegment(':', $line);
      $value = CutSegment(':', $line); if($data['reverseAction']) $value = CutSegment(':', $line);
      deviceCommand($deviceId, $paramName, $value);
    }
  }

  function handleCallLine($line, $data)
  {
    $rev2 = false;
    $evtDS = db()->getDS('events', $line);
    if(sizeof($evtDS) == 0)
    {
      $evtDS = db()->getDS('events', $line, 'e_address_rev');
      $rev2 = true;
    }
    $dataCopy = $data;
    $dataCopy['reverseAction'] = $rev2;
    $this->executeScript($evtDS['e_code'], $dataCopy);
  }
  
  function getAllDevices()
  {
    if(!$GLOBALS['allDevices'])
      foreach(db()->get('SELECT * FROM #devices') as $ds)
         $GLOBALS['allDevices'][$ds['d_key']] = $ds;
    return($GLOBALS['allDevices']);
  }
  
  function resolve($symbol, $data)
  {
    $vr = $data;
  	foreach(explode('.', $symbol) as $ni) 
  	  if(is_array($vr)) $vr = $vr[$ni]; else $vr = '';
  	return(first($vr, ''));
  }
  
  function handleSelectLine($line, $data, $doSelect = true)
  {
    $idx = $this->getAllDevices();
    foreach($data['fnParams'] as $select)
    {
      if($select == 'ALL') # select all
      {
        if($doSelect) $data['select'] = array();
        foreach($GLOBALS['allDevices'] as $k => $ds)
          $data['select'][$k] = $doSelect;
      }
      if($select == 'NONE') # select all
      {
        if($doSelect) $data['select'] = array();
      }
      if($select == 'OTHER') # select all
      {
        foreach($idx as $k => $ds)
          if(!isset($GLOBALS['log'][$k]))
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 4) == 'MAP=') 
      {
        CutSegment('=', $select);
        $mapSelect = $this->resolve($select, $data);
        $mappedDevice = $this->map[trim($mapSelect)];
        $GLOBALS['log'][] = 'MAP>'.$select.'>'.$mapSelect.'>'.$mappedDevice;
        if(isset($mappedDevice) && $mappedDevice != '')
        foreach($idx as $k => $ds)
          if($ds['d_key'] == $mappedDevice || $ds['d_alias'] == $mappedDevice || $ds['d_id'] == $mappedDevice)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 5) == 'TYPE=') 
      {
        CutSegment('=', $select);
        foreach($idx as $k => $ds)
          if($ds['d_type'] == $select || $ds['d_bus'].'-'.$ds['d_type'] == $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 6) == 'TYPE!=') 
      {
        CutSegment('=', $select);
        foreach($idx as $k => $ds)
          if($ds['d_type'] != $select && $ds['d_bus'].'-'.$ds['d_type'] != $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 5) == 'SUBTYPE=') 
      {
        CutSegment('=', $select);
        foreach($idx as $k => $ds)
          if($ds['d_icon'] == $select || $ds['d_bus'].'-'.$ds['d_icon'] == $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 6) == 'SUBTYPE!=') 
      {
        CutSegment('=', $select);
        foreach($idx as $k => $ds)
          if($ds['d_icon'] != $select && $ds['d_bus'].'-'.$ds['d_icon'] != $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 4) == 'PRI=') 
      {
        CutSegment('=', $select);
        foreach($idx as $k => $ds)
          if($ds['d_priority'] == $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 4) == 'PRI>') 
      {
        CutSegment('>', $select);
        foreach($idx as $k => $ds)
          if($ds['d_priority'] > $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 4) == 'PRI<') 
      {
        CutSegment('<', $select);
        foreach($idx as $k => $ds)
          if($ds['d_priority'] < $select)
            $data['select'][$k] = $doSelect;
      }
      else if(substr($select, 0, 6) == 'GROUP=') 
      {
        CutSegment('=', $select);
        $list = H2NVStore::get('group/'.$select);
        foreach($list as $deviceKey)
          $data['select'][$deviceKey] = $doSelect;
      }
      else if(substr($select, 0, 7) == 'GROUP!=') 
      {
        CutSegment('=', $select);
        $list = array();
        foreach(H2NVStore::get('group/'.$select) as $sl)
          $list[$sl] = true;
        foreach($idx as $k => $ds)
          if(!$list[$k])
            $data['select'][$k] = $doSelect;
      }
      else  
      {
        foreach($idx as $k => $ds)
          if($ds['d_key'] == $select || $ds['d_alias'] == $select || $ds['d_id'] == $select)
            $data['select'][$k] = $doSelect;
      }
   }
    $this->executeLine($data['line'], $data);
  }
  
  function handleAutoLine($line, $data)
  {
    if($data['fnValue'] == '') return;
    $idx = $this->getAllDevices();
    if(is_array($data['select'])) foreach($data['select'] as $k => $enabled)
    if($enabled)
    {
      db()->get('UPDATE devices 
        SET d_auto = ?
        WHERE d_key = ?', array($data['fnValue'], $k));
      broadcast(array('type' => 'dparam_auto', 'device' => $k, 'value' => $data['fnValue']));
    }
    $this->executeLine($data['line'], $data);
  }
  
  function handleSETLine($line, $data)
  {
    $param = $data['fnParams'][0];
    $value = $data['fnParams'][1];
    $rvalue = $data['fnParams'][2];
    if($data['reverseAction']) $value = $rvalue;
    if($value == '') return;
    $idx = $this->getAllDevices();
    if(is_array($data['select'])) foreach($data['select'] as $k => $enabled)
    if($enabled)
    {
      $ds = $idx[$k];
      deviceCommand($k, $param, $value);
      # todo: broadcast
    }
    $this->executeLine($data['line'], $data);
  }
  
  function handleMODELine($line, $data)
  {
    if($data['fnValue'] == '') return;
    $mode = new H2Mode();
    $mode->set($data['fnValue']);
    $this->executeLine($data['line'], $data);
  }
   
  function handleLINELOGLine($line, $data)
  {
    $data['event-fn'] = $fn;
    $data['event-params'] = $parts;
    $data['event-line'] = $line;
    WriteToFile('log/event.debug.log', json_encode($data).chr(10));
  }
  
  function handleLOGLine($line, $data)
  {
    $data['event-fn'] = $fn;
    $data['event-params'] = $parts;
    $data['event-line'] = $line;
    WriteToFile('log/event.debug.log', json_encode($GLOBALS['log']).chr(10));
  }
  
  function handleTHERMOSWITCHLine($line, $data)
  {
    $emitterName = CutSegment(':', $line);
    if(trim($emitterName) == '') $emitterName = $data['emitter_root'];
    $thermoDS = getDeviceDS($emitterName);
    if(sizeof($thermoDS) == 0) 
    {
      $GLOBALS['log'][] = 'THERMOSWITCH device not found: '.$emitterName;
      return;
    }
    $thermostat = getExtendedDeviceState($emitterName);
    $thermostat[$data['param']] = $data['value'];
    $data['thermo'] = $thermostat;
    $data['reverseAction'] = !(floatval($thermostat['SET_TEMPERATURE']) > floatval($thermostat['TEMPERATURE']));
    $GLOBALS['log'][] = 'THERMOSWITCH '.$thermostat['SET_TEMPERATURE'].' '.$thermostat['TEMPERATURE'].' '.$data['reverseAction'];
    $this->executeLine($data['line'], $data);
  }
  
  function executeLine($line, $data)
  {
    $line = trim($line);
    if($line == '' || substr($line, 0, 1) == '#') return;
    # if the line starts with : it's a device command
    if(substr($line, 0, 1) == ':')
    {
      $this->handleDeviceLine($line, $data);
    }
    # if it starts with a > it's an extended command
    else 
    {
      $thisCommand = CutSegment('/', $line);
      $parts = explode(':', $thisCommand);
      CutSegment(':', $thisCommand);
      $fn = strtoupper(array_shift($parts));
      $data['fnParams'] = $parts;
      $data['line'] = $line;
      if($data['reverseAction']) $data['fnValue'] = $parts[1]; else $data['fnValue'] = $parts[0];

      $GLOBALS['log'][] = '('.$fn.' '.implode(' ', $parts).')';

      switch($fn)
      {
        case('PHP'):
        {
          eval($line);
          break;
        }          
        case('MAP'):
        {
          $mapKey = CutSegment('>', $thisCommand);
          $this->map[trim($mapKey)] = trim($thisCommand);
          break;
        }          
        case('DAY?'):
        {
          if(getSunStatus() == 'day') $this->executeLine($line, $data);
          break;
        }          
        case('NIGHT?'):
        {
          if(getSunStatus() == 'night') $this->executeLine($line, $data);
          break;
        }          
        case('REV?'):
        {
          if($data['reverseAction']) 
          {
            $data['reverseAction'] = !$data['reverseAction'];
            $this->executeLine($line, $data);
          }
          break;
        }          
        case('ACTION?'):
        {
          if(!$data['reverseAction']) $this->executeLine($line, $data);
          break;
        }          
        case('MODE?'):
        {
          $mode = new H2Mode();
          if(strtoupper($mode->current) == strtoupper($thisCommand))
            $this->executeLine($line, $data);
          break;
        }
        case('SELECT'):
        {
          $this->handleSelectLine($thisCommand, $data, true);
          break;
        }
        case('LINELOG'):
        {
          $this->handleLINELOGLine($thisCommand, $data, true);
          break;
        }
        case('LOG'):
        {
          $this->handleLOGLine($thisCommand, $data, true);
          break;
        }
        case('REMOVE'):
        {
          $this->handleSelectLine($thisCommand, $data, false);
          break;
        }
        default: 
        {
          $fname = 'handle'.$fn.'Line';
          call_user_method($fname, $this, $thisCommand, $data);
          break;
        }
      } 
    }
  }

  function executeScript($code, $data)
  {
    $GLOBALS['command-mode'] = 'trigger';
    $ds = $data['eventDS'];
    if(isset($ds))
      $GLOBALS['command-source'] = '#'.$ds['e_key'].' '.first($data['emitter_name'], $data['event']);
    foreach($data as $k => $v) $$k = $v;
    $code = trim($code);
    
    ob_start();

    if(strtoupper(substr($code, 0, 4)) == '#PHP')
    {
      foreach($data as $k => $v)
        $$k = &$data[$k];
      $command = function($line) use ($data) {
        $this->executeLine($line, $data);
        };
      eval($code);
      $result = ob_get_clean();
      if($log)
        WriteToFile('log/event.debug.log', $result.chr(10));
    }
    else
    {
      $lines = explode(chr(10), $code);
      foreach($lines as $line)
      {
        $data['select'] = array();
        $this->executeLine($line, $data);
      }
      $result = ob_get_clean();
    }

    return($result);    
  }

  function callHandlers($handlers, $data)
  {
    foreach(array('', '_rev') as $addressType)
    {
      $reverseAction = $addressType == '_rev';
      $where = array();
      foreach($handlers as $h)
        $where[] = 'e_address'.$addressType.' = ?';
      foreach(o(db)->get('SELECT * FROM events
        WHERE ('.implode(' OR ', $where).')
        ORDER BY e_order ASC', $handlers) as $eds)
      {
        $GLOBALS['log'][] = 'event handler called: '.$eds['e_address'.$addressType];
        broadcast(array('type' => 'eventHandled', 'address' => $eds['e_address'.$addressType]));
        $this->ignoreExecution = false;
        $emitterRoot = $data['device']; $emitterRoot = CutSegment(':', $emitterRoot);  
        if($emitterRoot != $data['device'])
          $emitterRootDS = getDeviceDS($emitterRoot); else $emitterRootDS = $data['ds'];
        $this->executeScript($eds['e_code'], array(
          'emitter_id' => $data['device'],
          'emitter_param' => $data['param'],
          'emitter_value' => $data['value'],
          'emitter_alias' => $emitterRootDS['d_alias'],
          'emitter_root' => $emitterRoot,
          'emitter_name' => first($emitterRootDS['d_name'], $emitterRootDS['d_id']),
          'emitter' => $data['ds'],
          'call' => $data,
          'eventDS' => &$eds,
          'reverseAction' => $reverseAction,
          'event' => $eds['e_address'.$addressType],
          ));
        if(!$this->ignoreExecution)
          o(db)->query('UPDATE events SET e_lastcalled = '.time().' WHERE e_key = '.$eds['e_key']);
      }
    }
    return($this);
  }
  
  function triggerEventByName($eventName, $data = array())
  {
    //broadcast(array('type' => 'eventTriggered', 'address' => $eventName));
    return($this->callHandlers(array($eventName), $data, true));
  }

}