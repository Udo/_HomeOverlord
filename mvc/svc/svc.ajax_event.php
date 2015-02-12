<?php

  $isDay = $t >= strtotime(date('H:i', getSunrise(false))) && $t <= strtotime(date('H:i', getSunset(false)));
  $dayString = $isDay ? 'DAY' : 'NIGHT';

  $data = json_decode($_REQUEST['data'], true);
  $hdl = H2EventManager::getApplicableEventsForDevice($data);

  $dds = o(db)->getDSMatch('devices', array(
    'd_bus' => $data['type'],
    'd_id' => $data['device'],
    ));
  
  $data['ds'] = $dds;
  
  $this->callEventHandlers(
    $hdl, 
    $data);
    
  profile_point('done');
  WriteToFile('log/event.log', date('Y-m-d H:i:s').' ext event '.$_REQUEST['data'].chr(10)
    .'- '.json_encode($hdl).chr(10)
    .'- '.json_encode($GLOBALS['log']).chr(10)
    #.'- '.json_encode($GLOBALS['profiler_log']).chr(10)
    );
  
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
