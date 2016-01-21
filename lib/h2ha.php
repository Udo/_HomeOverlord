<?

$GLOBALS['weekdays'] = array(
  1 => 'MONDAY', 2 => 'TUESDAY', 3 => 'WEDNESDAY', 4 => 'THURSDAY', 5 => 'FRIDAY', 6=> 'SATURDAY', 7 => 'SUNDAY'
  );

define('STATE', 'STATE');

$GLOBALS['config']['defaultParams'] = array(
  'Light' => 'STATE',
  'Blinds' => 'LEVEL',
  );

function timeZoneOffset()
{
  return($tzOffset = date('O')/100);
}

function getSunset($m = true)
{
  $d = (
    date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, 
    $GLOBALS['config']['geo']['lat'], $GLOBALS['config']['geo']['long'], $GLOBALS['config']['geo']['zenith'], 
    timeZoneOffset()));
  if($m) $d = dateToMinutes($d);
  return($d);
}

function getSunrise($m = true)
{
  $d = (
    date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, 
    $GLOBALS['config']['geo']['lat'], $GLOBALS['config']['geo']['long'], $GLOBALS['config']['geo']['zenith'], 
    timeZoneOffset()));
  if($m) $d = dateToMinutes($d);
  return($d);
}

function dateToMinutes($d)
{
  return((date('H', $d)*60)+date('i', $d));
}

function getSunStatus()
{
  if($_REQUEST['scheme']) return($_REQUEST['scheme']);
  $cm = dateToMinutes(time());
  $sunSet = getSunset();
  $sunRise = getSunrise();
  if($cm > $sunSet || $cm < $sunRise) return('night'); else return('day');
}

function hours($h)
{
  return($h*60);
}

function queryCommandServer($query, $msg = false)
{
  if($msg) $query['msg'] = json_encode($msg);
  $response = cqrequest(array(array('url' => 'http://localhost:1080/?'.http_build_query($query))));
  return($response['data']);
}

function broadcast($msg)
{
  $msg['cmd'] = 'broadcast';
  cqrequest(array(array('url' => 'http://localhost:1080/?'.http_build_query($msg))));
}

function systemMessage($msgType, $text = '(no text)', $data = array())
{
  $mds = array(
    'm_type' => $msgType,
    'm_time' => time(),
    'm_text' => $text,
    'm_data' => json_encode($data),
    );
  $mkey = o(db)->commit('messages', $mds);
  cqrequest(array(array('url' => 'http://localhost:1080/?'.http_build_query(array(
    'cmd' => 'broadcast',
    'type' => 'message',
    'msgtype' => $msgType,
    'text' => $text,
    'key' => $mkey,
    )))));
  return($mds);
}

function getServiceFlags()
{
  return(dataCache('hm-service-flags', function() {
    $hmSvc = array();
    foreach(HMRPC('getServiceMessages') as $svc)
    {
      $id = $svc[0];
      $did = CutSegment(':', $id);
      $hmSvc[$did][] = $svc; 
    }
    return($hmSvc);
  }));
}

function getExtendedDeviceState($deviceId)
{
  $stateInfo = array();
  foreach(o(db)->get('SELECT si_param,si_value FROM stateinfo WHERE si_mode = "RX" && si_name LIKE "'.so($deviceId).'%"') as $tds)
    $stateInfo[$tds['si_param']] = $tds['si_value'];
  return($stateInfo);
}

function HMRPC($method, $cmd = false)
{
  require_once("ext/HM-XMLRPC-Client/Client.php");
  $Client = new \XMLRPC\Client("localhost", 2001);
  try
  {
    $params = array();
    if(is_array($cmd)) foreach($cmd as $c) if(!is_array($c))
    {
      if($c == 'true')
        $params[] = true;
      else if($c + 0 > 0)
        $params[] = floatval($c);
      else if($c == "0")
        $params[] = $c+0;
      else 
        $params[] = $c;
    }
    else
      $params[] = $c;
    $result = $Client->send($method, $params);
    return($result);
  }
  catch (\XMLRPC\XMLRPCException $ex)
  {
    return(array('error' => $ex->getMessage()));
  }
}

function reviewParams($ds, $commandType, $value)
{
  if($ds['d_config'] != '')
  {
    $cfg = json_decode($ds['d_config'], true);
    if($cfg['direction'])
      $value = 1.0 - $value; 
  }
  return($value);
}

function sendToNode($cmd, $params)
{
  $params['cmd'] = $cmd;
  $reqUrl = 'http://localhost:1080/?'.http_build_query($params);
  cqrequest(array(array('url' => $reqUrl)));
}

function recordDeviceStatus($device, $commandType, $value, $reason)
{
  $sds = array(
    'si_bus' => $device['d_bus'],
    'si_name' => $device['d_id'],
    'si_param' => $commandType,
    'si_value' => $value,
    'si_time' => time(),
    'si_devicekey' => $device['d_key'],
    'si_by' => $by,
    'si_event' => $GLOBALS['command-source'],
    'si_mode' => 'TX',
    'si_uid' => $_SESSION['uid']+0,
    'si_ip' => first($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']),
    );
  o(db)->commit('stateinfo', $sds);
  $device['d_state'] = $value;
  $device['d_statustext'] = $reason;
  $device['d_statuschanged'] = time();
  o(db)->commit('devices', $device);
}

function sendHECommand($device, $commandType, $value, $reason = 'unknown')
{
  if(sizeof($device) > 0 && $device['d_state'] != $value)
  {
    $pv = $value+0;
    $pv = reviewParams($device, $commandType, $pv);
    $reqUrl = 'http://localhost:1080/?cmd=update&bus='.$device['d_bus'].
      '&param='.$commandType.
      '&key='.($device['d_key']).
      '&stxt='.urlencode($reason).
      '&id='.($device['d_id']).
      '&value='.($pv);
      cqrequest(array(array('url' => $reqUrl)));
    recordDeviceStatus($device, $commandType, $pv, $reason);
  }
}

function sendGPIOCommand($device, $commandType, $value, $reason = 'unknown')
{
  if(sizeof($device) > 0)
  {
    $pv = $value+0;
    $pv = reviewParams($device, $commandType, $pv);
    $reqUrl = 'http://localhost:1080/?cmd=gpio&bus='.$device['d_bus'].
      '&param='.$commandType.
      '&key='.($device['d_key']).
      '&stxt='.urlencode($reason).
      '&id='.($device['d_id']).
      '&value='.($pv);
      cqrequest(array(array('url' => $reqUrl)));
    //recordDeviceStatus($device, $commandType, $pv, $reason);
  }
}

function sendHMCommand($device, $commandType, $value, $reason = 'unknown', $config = array(), $fireEvent = false)
{
  if(sizeof($device) > 0 && $device['d_state'] != $value)
  {
    $pv = reviewParams($device, $commandType, $value);
    if($commandType == 'STATE')
      $hpv = $pv == 0 ? 'false' : 'true';
    else
      $hpv = $pv;
    // send HM commands directly, to save time
    $result = HMRPC('setValue', array($device['d_id'], $commandType, $hpv));
    
    queryCommandServer(array(
      'cmd' => 'busmessage',
      'fireevent' => $fireEvent ? 'Y' : 'N',
      'data' => json_encode(array(
        'key' => $device['d_key'],
        'type' => $device['d_bus'],
        'device' => $device['d_id'],
        'param' => $commandType,
        'value' => $pv,
        'stxt' => $reason,
        )),
      ));
    recordDeviceStatus($device, $commandType, $pv, $reason);
    $tmr = $config['timer_'.$commandType.'_'.$pv];
    if($tmr)
    {
      cqrequest(array(array('url' => 'http://localhost:1080/?cmd=timer'.
        '&name='.$device['d_key'].
        '&countDown='.$tmr['seconds'].
        '&stxt='.urlencode($reason).
        '&param='.urlencode($commandType).
        '&key='.($device['d_key']).
        '&id='.($device['d_id']).
        '&trigger='.('timer_'.$commandType.'_'.$pv))));
    }
  }
  return($result);
}

function groupCommand($groupKey, $command, $by = 'API')
{
  $g = db()->getDS('groups', $groupKey);
  $deviceConfig = array();
  if($g['g_deviceconfig'] != '')
    $deviceConfig = json_decode($g['g_deviceconfig'], true);
  $states = explode(',', first($g['g_states'], 'off,on'));
  
  foreach($deviceConfig as $deviceKey => $stateValue) if(isset($stateValue[$command]) && trim($stateValue[$command]) != '')
  {
    print('SET '.$deviceKey.' '.$commandType.' TO '.$stateValue[$command].chr(10));
    //deviceCommand($deviceKey, $commandType, $stateValue[$command], $by);
  }
}

function getDeviceDS($deviceIdentifier)
{
  $device = o(db)->getDS('devices', $deviceIdentifier);
  if(sizeof($device) == 0) $device = o(db)->getDS('devices', $deviceIdentifier, 'd_alias');
  if(sizeof($device) == 0) $device = o(db)->getDS('devices', $deviceIdentifier, 'd_id');
  return($device);
}

function deviceCommand($deviceKey, $commandType, $value, $by = 'API', $fireEvent = false)
{
  $device = getDeviceDS($deviceKey);
  if(!approveAction(array(
    'type' => 'deviceCommand', 'device' => $deviceKey, 
    'deviceType' => $device['d_type'], 'ds' => $device,
    'command' => $commandType, 'value' => $value, 'by' => $by))) return;
  if($device['d_auto'] != 'A' && $GLOBALS['command-mode'] == 'trigger') return;
  $GLOBALS['log'][$device['d_key']] = array('type' => 'deviceCommand', 'device' => $deviceKey, 'param' => $commandType, 'value' => $value);
  $config = json_decode($device['d_config'], true);
  if(sizeof($device) > 0 && $device['d_state'] != $value)
  {
    if($device['d_bus'] == 'HE')
    {
      $pv = $value+0;
      $pv = reviewParams($device, $commandType, $pv);
      $reqUrl = 'http://localhost:1080/?cmd=update&bus='.$device['d_bus'].
        '&param='.$commandType.
        '&key='.($device['d_key']).
        '&stxt='.urlencode(first($GLOBALS['command-source'], $by)).
        '&id='.($device['d_id']).
        '&value='.($pv);
      cqrequest(array(array('url' => $reqUrl)));
    }
    else if($device['d_bus'] == 'HM')
    {
      sendHMCommand($device, $commandType, $value, first($GLOBALS['command-source'], $by), $config, $fireEvent);
      return;
    }

    $sds = array(
      'si_bus' => $device['d_bus'],
      'si_name' => $device['d_id'],
      'si_param' => $commandType,
      'si_value' => $pv,
      'si_time' => time(),
      'si_devicekey' => $device['d_key'],
      'si_by' => $by,
      'si_event' => $GLOBALS['command-source'],
      'si_mode' => 'TX',
      'si_uid' => $_SESSION['uid']+0,
      'si_ip' => first($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['REMOTE_ADDR']),
      );
    o(db)->commit('stateinfo', $sds);

    /*WriteToFile('log/stats.'.gmdate('Y-m').'.log', 
      json_encode(array(
        'type' => 'dp', 'key' => $device['d_key'], 'id' => $device['d_id'], 'bus' => $device['d_bus'], 'param' => $commandType, 'value' => $pv, 'tr' => 'tx')).
      chr(10)
      );*/

    $device['d_state'] = $value;
    $device['d_statustext'] = first($GLOBALS['command-source'], $by);
    $device['d_statuschanged'] = time();
    o(db)->commit('devices', $device);
  }
}

?>