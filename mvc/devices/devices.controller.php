<?php

class DevicesController extends H2Controller
{
  function __init()
  {
    $this->access('local,internal,auth');
    $GLOBALS['submenu'] = array();
    foreach(array('index', 'admin') as $a)
      $GLOBALS['submenu'][] = array('controller' => 'devices', 'action' => $a, 'text' => 'devices.'.$a);
  }

  function index()
  {
    $GLOBALS['pagetitle'] = ':: Devices';
    $this->setupDeviceList();
  }
  
  function pairHm()
  {
  
  }
  
  function admin()
  {
  
  }
  
  function ajax_savefield()
  {
    o(db)->query('UPDATE devices SET '.$_REQUEST['f'].'=? WHERE d_key=?', array($_REQUEST['v'], $_REQUEST['key']));
    WriteToFile('log/debug.log', json_encode($_REQUEST).chr(10));
  }
  
  function show()
  {
  
  }
  
  function ajax_pairHmStart()
  { 
    HMRPC('setInstallMode', array(true));
    print(HMRPC('getInstallMode', array()));    
  }
  
  function pair()
  {
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
        print('You have 60 seconds to pair your device. After completing the pairing, click here to create an entry for it: <a href="'.
          actionUrl('pair', 'devices').
          '">Pairing Complete</a>.');
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
  
  function params()
  {
  
  }
  
  function clients()
  {
    
  }
  
  function client_settings()
  {
    $this->setupDeviceList();
  }
  
  function ajax_client_update()
  {
    $nv = new H2NVStore();
    $clientIdentifier = $_POST['id'];
    $clientSettings = $nv->get($clientIdentifier);
    $clientSettings[$_POST['key']] = $_POST['value'];
    $nv->set($clientIdentifier, $clientSettings);
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
  
  function ajax_halcommand()
  {
    $this->skipView = true;
    WriteToFile('log/hal.command.log', json_encode($_POST).chr(10));
    $device = new H2HALDevice($_POST['key']);
    $call = first($_POST['call'], 'state');
    $device->{$call}($_POST['command'], first($_POST['by'], 'EXT'));
  }
  
  function _getSubmenu2()
  {
    foreach(array(
      'admin', 'clients', 'show', 'cli', 'pairhm', 'groups',
      ) as $act)
      $submenu[] = '<a style="'.($_REQUEST['action'] == $act ? 'font-weight:bold;' : '').'" href="'.
        actionUrl($act).
        '">'.l10n($_REQUEST['controller'].'.'.$act).'</a>';
    
    return('<div class="submenu2">'.implode(' | ', $submenu).'</div>');
  }

}

