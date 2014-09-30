<?php

  $isDay = $t >= strtotime(date('H:i', getSunrise(false))) && $t <= strtotime(date('H:i', getSunset(false)));
  $dayString = $isDay ? 'DAY' : 'NIGHT';

  $data = json_decode($_REQUEST['data'], true);
  $hdl = array(
      'all', 
      $data['type'],
      $data['type'].'-'.$data['device'],
      $data['type'].'-'.$data['param'],
      $data['type'].'-'.$data['device'].'-'.$data['param'],
      $data['type'].'-'.$data['device'].'-'.$data['param'].'-'.$data['value'],
      $data['type'].'-'.$data['device'].'-'.$dayString.'-'.$data['param'],
      $data['type'].'-'.$data['device'].'-'.$dayString.'-'.$data['param'].'-'.$data['value'],
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
