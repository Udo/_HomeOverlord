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
  WriteToFile('log/stats.'.gmdate('Y-m').'.log', 
    json_encode(array(
      'type' => 'dp', 'key' => $dds['d_key'], 'id' => $data['device'], 'bus' => $data['type'], 'param' => $data['param'], 'value' => $data['value'], 'tr' => 'rx')).
    chr(10)
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
