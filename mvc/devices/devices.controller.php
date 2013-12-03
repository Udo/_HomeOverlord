<?php

class DevicesController extends H2Controller
{
  function __init()
  {
    $this->access('local,internal,auth');
    $GLOBALS['submenu'] = array();
    foreach(array('index', 'groups', 'cli') as $a)
      $GLOBALS['submenu'][] = array('controller' => 'devices', 'action' => $a, 'text' => 'devices.'.$a);
  }

  function index()
  {
    $GLOBALS['pagetitle'] = ':: Devices';
    $this->setupDeviceList();
  }
  
  function pair()
  {
    $this->devices = array();
    foreach(o(db)->get('SELECT * FROM devices 
      ORDER BY d_type, d_key DESC') as $dds)
    {
      $this->devices[$dds['d_bus'].':'.$dds['d_id']] = $dds;
    }
    $this->hmDevices = HMRPC('listDevices', array());
    foreach($this->hmDevices as $h)
    {
      switch($h['TYPE'])
      {
        case('KEY'):
        {
          if(!isset($this->devices['HM:'.$h['ADDRESS']]))
          {
            // unknown device, make new dataset
            $dds = array(
              'd_bus' => 'HM',
              'd_type' => 'Key',
              'd_room' => 'unknown',
              'd_name' => 'New Key '.date('Y-m-d H:i:s'),
              'd_id' => $h['ADDRESS'],
              );
            $dds['d_key'] = o(db)->commit('devices', $dds);
            $this->devices['HM:'.$h['ADDRESS']] = $dds;
          }
          $this->devices['HM:'.$h['ADDRESS']]['info'] = $h;
          break;
        }
        case('BLIND'):
        case('SWITCH'):
        {
          if(!isset($this->devices['HM:'.$h['ADDRESS']]))
          {
            // unknown device, make new dataset
            $dds = array(
              'd_bus' => 'HM',
              'd_type' => $h['TYPE'] == 'BLIND' ? 'Blinds' : 'Light',
              'd_room' => 'unknown',
              'd_name' => 'New '.($h['TYPE'] == 'BLIND' ? 'Blinds' : 'Switch').' '.date('Y-m-d H:i:s'),
              'd_id' => $h['ADDRESS'],
              );
            $dds['d_key'] = o(db)->commit('devices', $dds);
            $this->devices['HM:'.$h['ADDRESS']] = $dds;
          }
          $this->devices['HM:'.$h['ADDRESS']]['info'] = $h;
          break;
        }
      }      
    }    
  }
  
  function bigicons()
  {
    $this->index();
  }
  
  function setupDeviceList($by = 'd_room')
  {
    $this->devices = array();
    foreach(o(db)->get('SELECT * FROM devices
      ORDER BY d_room, d_key') as $d)
    {
      if($d['d_room'] != 'unknown')
        $this->devices[$d[$by]][] = $d;
    }  
  }
  
  function cli()
  {
  
  }
  
  function ajax_cli()
  {
    $cmd = explode(' ', trim($_REQUEST['q']));
    $method = array_shift($cmd);
    if(substr($method, 0, 3) == 'hm.')
    {
      print_r(HMRPC(substr($method, 3), $cmd));
      if($method == 'hm.setInstallMode')
      {
        print('You have 60 seconds to pair your device. After completing the pairing, click here to create an entry for it: <a href="?controller=devices&action=pair">Pairing Complete</a>.');
      }
    }
    else
    {
      profile_point('starting command');
      eval(trim($_REQUEST['q']));
      profile_point('command executed');
      print(chr(10));
      print_r($GLOBALS['profiler_log']);
    }
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
  
  function groups()
  {
  
  }
  
  function group()
  {
    $this->setupDeviceList();
  }
  
  function group_new()
  {
    $gds = array('g_name' => $_REQUEST['name']);
    o(db)->commit('groups', $gds);
    $this->viewName = 'groups';
  }
  
  function group_delete()
  {
    o(db)->remove('groups', $_REQUEST['id']);
    $this->viewName = 'groups';
  }
  
  function ajax_switch()
  {
    $this->skipView = true;
    deviceCommand($_REQUEST['key'], first($_REQUEST['p'], 'STATE'), $_REQUEST['v'], first($_REQUEST['by'], 'EXT'));
  }

}

