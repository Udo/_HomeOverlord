<?php

function rev($reverseAction, $a, $b)
{
  if($reverseAction)
    return($b);
  else
    return($a);
}

class H2Event 
{

  function handleDeviceLine($line, $data)
  {
    $reverseAction = $data['reverseAction'];
    $seg = explode(':', substr($line, 1));
    if($seg[0] == 'HAL') 
    {
      $dev = new H2HALDevice($seg[1]);
      $dval = rev($reverseAction, $seg[2], $seg[3]);
      $dev->state($dval, $GLOBALS['command-source']);
    }
    else 
    {
      $dval = rev($reverseAction, $seg[2], $seg[3]);
      if(trim($seg[0]) != '' && sizeof($seg) > 2 && $dval != '' && $dval != '-')
      {
        deviceCommand($seg[0], $seg[1], $dval);
      }
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
  
  function handleSelectLine($line, $data, $doSelect = true)
  {
    $idx = $this->getAllDevices();
    if(substr($line, 0, 1) == '>')
      $this->executeLine($line, $data);
    else
    {
      $select = CutSegment(':', $line);
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
        $GLOBALS['log'][] = 'SELECT GROUP '.json_encode($list);
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
      $this->handleSelectLine($line, $data, $doSelect);
    }
  }
  
  function handleBlockLine($line, $data)
  {
    $value = CutSegment(':', $line);
    $rvalue = CutSegment(':', $line);
    if($data['reverseAction']) $value = $rvalue;
    if($value == '') return;
    $idx = $this->getAllDevices();
    if(is_array($data['select'])) foreach($data['select'] as $k => $enabled)
    if($enabled)
    {
      db()->get('UPDATE devices 
        SET d_auto = ?
        WHERE d_key = ?', array($value, $k));
      broadcast(array('type' => 'dparam_auto', 'device' => $k, 'value' => $value));
    }
  }
  
  function handleSetLine($line, $data)
  {
    $param = CutSegment(':', $line);
    $value = CutSegment(':', $line);
    $rvalue = CutSegment(':', $line);
    if($data['reverseAction']) $value = $rvalue;
    if($value == '') return;
    $idx = $this->getAllDevices();
    $GLOBALS['log'][] = 'SET '.json_encode($data['select']);
    if(is_array($data['select'])) foreach($data['select'] as $k => $enabled)
    if($enabled)
    {
      $ds = $idx[$k];
      deviceCommand($k, $param, $value);
      # todo: broadcast
    }
  }
  
  function executeLine($line, $data)
  {
    # if the line starts with : it's a device command
    if(substr($line, 0, 1) == ':')
    {
      $this->handleDeviceLine($line, $data);
    }
    # if it starts with a > it's an extended command
    else if(substr($line, 0, 1) == '>')
    {
      $seg = CutSegment(':', $line);
      switch($seg)
      {
        case('>DAY'):
        {
          if(getSunStatus() == 'day') $this->executeLine($line, $data);
          break;
        }          
        case('>NIGHT'):
        {
          if(getSunStatus() == 'night') $this->executeLine($line, $data);
          break;
        }          
        case('>REV'):
        {
          if($data['reverseAction']) 
          {
            $data['reverseAction'] = !$data['reverseAction'];
            $this->executeLine($line, $data);
          }
          break;
        }          
        case('>ACTION'):
        {
          if(!$data['reverseAction']) $this->executeLine($line, $data);
          break;
        }          
        case('>CALL'):
        {
          $this->handleCallLine($line, $data);
          break;
        }
        case('>SELECT'):
        {
          $this->handleSelectLine($line, $data, true);
          break;
        }
        case('>REMOVE'):
        {
          $this->handleSelectLine($line, $data, false);
          break;
        }
        case('>AUTO'):
        {
          $this->handleBlockLine($line, $data);
          break;
        }
        case('>SET'):
        {
          $this->handleSetLine($line, $data);
          break;
        }
        default: 
        {
          $GLOBALS['log'][] = 'unknown command: '.$seg;
          break;
        }
      }        
    }
    else 
    {
      $GLOBALS['log'][] = 'unrecognized: '.$line;
    }
  }

  function executeScript($code, $data)
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
        $this->executeLine(trim($line), $data);
      }
    }
    else 
    {
      eval($code);
    }
    return(ob_get_clean());    
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
        broadcast(array('type' => 'eventHandled', 'address' => $eds['e_address'.$addressType]));
        $this->ignoreExecution = false;
        $this->executeScript($eds['e_code'], array(
          'emitter_id' => $data['device'],
          'emitter_param' => $data['param'],
          'emitter_value' => $data['value'],
          'emitter_name' => first($data['ds']['d_name'], $data['ds']['d_id']),
          'ds' => &$eds,
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
    broadcast(array('type' => 'eventTriggered', 'address' => $eventName));
    return($this->callHandlers(array($eventName), $data, true));
  }

}