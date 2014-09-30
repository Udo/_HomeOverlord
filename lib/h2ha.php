<?

$GLOBALS['weekdays'] = array(
  1 => 'MONDAY', 2 => 'TUESDAY', 3 => 'WEDNESDAY', 4 => 'THURSDAY', 5 => 'FRIDAY', 6=> 'SATURDAY', 7 => 'SUNDAY'
  );

define('STATE', 'STATE');

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
  $hmSvc = array();
  foreach(HMRPC('getServiceMessages') as $svc)
  {
    $id = $svc[0];
    $did = CutSegment(':', $id);
    $hmSvc[$did][] = $svc; 
  }
  return($hmSvc);
}

function HMRPC($method, $cmd)
{
  require_once("ext/HM-XMLRPC-Client/Client.php");
  $Client = new \XMLRPC\Client("localhost", 2001);
  try
  {
    $params = array();
    foreach($cmd as $c) if(!is_array($c))
    {
      if($c == 'true')
        $params[] = true;
      else if($c + 0 > 0)
        $params[] = $c+0;
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
  WriteToFile('log/switch.log', 'status change: '.json_encode($device).chr(10));
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

function sendHMCommand($device, $commandType, $value, $reason = 'unknown', $config = array())
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
    // notify clients
    $reqUrl = 'http://localhost:1080/?cmd=broadcast&bus='.$device['d_bus'].
      '&param='.$commandType.
      '&type=devicestatus'.
      '&stxt='.urlencode($reason).
      '&key='.($device['d_key']).
      '&id='.($device['d_id']).
      '&value='.($pv);
    cqrequest(array(array('url' => $reqUrl)));
    WriteToFile('log/switch.log', 'switch: '.$reqUrl.chr(10));
    recordDeviceStatus($device, $commandType, $pv, $reason);
    $tmr = $config['timer_'.$commandType.'_'.$pv];
    WriteToFile('log/switch.log', $device['d_key'].' timer prodded: '.'timer_'.$commandType.'_'.$pv.' ('.sizeof($tmr).'/'.sizeof($config).')'.chr(10));
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

function deviceCommand($deviceKey, $commandType, $value, $by = 'API')
{
  $device = o(db)->getDS('devices', $deviceKey, 'd_alias');
  if(sizeof($device) == 0) $device = o(db)->getDS('devices', $deviceKey);
  if(!approveAction(array(
    'type' => 'deviceCommand', 'device' => $deviceKey, 
    'deviceType' => $device['d_type'], 'ds' => $device,
    'command' => $commandType, 'value' => $value, 'by' => $by))) return;
  if($device['d_auto'] != 'A' && $GLOBALS['command-mode'] == 'trigger') return;
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
      sendHMCommand($device, $commandType, $value, first($GLOBALS['command-source'], $by), $config);
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
    WriteToFile('log/switch.log', json_encode($sds).chr(10));

    $device['d_state'] = $value;
    $device['d_statustext'] = first($GLOBALS['command-source'], $by);
    $device['d_statuschanged'] = time();
    o(db)->commit('devices', $device);
  }
}

?>