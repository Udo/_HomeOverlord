<?php
  
  class H2Zwave
  {
    
    static $apiState = false;
    
    static $sensorTypes = array(
      'temperature' => array('short-name' => 'Temp', 'icon' => 'thermometer-half'),
      'alarm_smoke' => array('short-name' => 'Smoke', 'icon' => 'fire', 'onoff' => true),
      'battery' => array('short-name' => 'Bat', 'icon' => 'battery'),
      'alarm_heat' => array('short-name' => 'Heat', 'icon' => 'warning', 'onoff' => true),
      'switchControl' => array('short-name' => 'Sw', 'icon' => 'toggle-on'),
      'switchBinary' => array('short-name' => 'Sw', 'icon' => 'toggle-on'),
      'meterElectric_kilowatt_hour' => array('short-name' => false, 'icon' => false),
      'meterElectric_watt' => array('short-name' => false, 'icon' => false),
      'meterElectric_voltage' => array('short-name' => false, 'icon' => false),
      'meterElectric_ampere' => array('short-name' => false, 'icon' => false),
      'luminosity' => array('short-name' => false),
      'alarm_burglar' => array('short-name' => false, 'icon' => 'hand-stop-o', 'onoff' => true),
      'tamper' => array('short-name' => false, 'icon' => 'signing', 'onoff' => true),
      'alarm_flood' => array('short-name' => false, 'icon' => 'tint', 'onoff' => true),
      'general_purpose' => array('short-name' => false, 'icon' => 'question-circle-o', 'onoff' => true),
      'PIR' => array('short-name' => false, 'icon' => 'circle', 'onoff' => true),
      );
      
    static function deviceCommand($deviceName, $command, $gateway = false) 
    { 
      if(!$gateway)
        $gateway = $GLOBALS['config']['zwave']['gateways'][0];
      # http://zway1:8083/ZAutomation/api/v1/devices/ZWayVDev_zway_5-0-37/command/on
      $url = $gateway['url'].'ZAutomation/api/v1/devices/'.$deviceName.'/command/'.$command;
      $req = httpRequest(
        $url, array(), array('headers' => array(
          'Cookie: ZWAYSession='.(self::$apiState['sid']),
        )));
      $data = json_decode($req['body'], true);
      if($data['code'] == '401')
      {
        $data['login-required'] = 'yes';
        $data['login-data'] = self::DeviceLogin($gateway);
        if($data['login-data']['auth']['data']['sid']) # looks good, try again
        {
          $req = httpRequest(
            $url, array(), array('headers' => array(
              'Cookie: ZWAYSession='.(self::$apiState['sid']),
            )));
          $data = json_decode($req['body'], true);
        }
      }
      $data['url'] = $url;
      return($data);
    }
      
    static function getDeviceInfo($vdev)
    {
      $deviceType = first($vdev['probeType'], $vdev['deviceType']);
      return(self::$sensorTypes[$deviceType]);
    }    
      
    static function getSensorName($vdev)
    {
      $deviceInfo = first(
        self::$sensorTypes[$vdev['metrics']['title']],
        self::$sensorTypes[$vdev['probeType']],
        self::$sensorTypes[$vdev['deviceType']]
        );
      if($deviceInfo['icon'])
        return('<i class="fa fa-'.$deviceInfo['icon'].'"></i>');
      else
      {
        if($deviceInfo['short-name'] === false)
          return('');
        return(first($deviceInfo['short-name'], $vdev['probeType'], '???'));
      }
    }
    
    static function GetState()
    {
      if(self::$apiState === false)
      {
        if(file_exists('data/zwave.state.json'))
          self::$apiState = json_decode(file_get_contents('data/zwave.state.json'), true);
        else
          self::$apiState = array();
      }
      return(self::$apiState);
    }
    
    static function SaveState()
    {
      file_put_contents('data/zwave.state.json', json_encode(self::$apiState));
    }
    
    static function DeviceLogin($gateway)
    {
      self::GetState();
      
      $remote_req = httpRequest(
        $gateway['url'].'ZAutomation/api/v1/system/remote-id');
      
      #return(json_decode($req['body'], true));
      $postData = json_encode(array(
        'login' => $gateway['username'],
        'password' => $gateway['password'],
        'rememberme' => true,
        ));
      
      $login_req = httpRequest(
        $gateway['url'].'ZAutomation/api/v1/login',
        $postData, array('headers' => array(
            'Content-Type: application/json;charset=UTF-8',
          ))
        );
        
      $authData = json_decode($login_req['body'], true);
        
      if($authData['data']['sid'])
      {
        self::$apiState = $authData['data'];
        self::SaveState();
      }
        
      return(array(
        #'remote' => $authData,
        'api' => self::$apiState,
        'auth' => $authData,
        'auth-headers' => $login_req['headers'],
        ));
    }
    
    static function RetrieveDeviceData($gateway, $autoLogin = true)
    {
      self::GetState();
      $req = httpRequest(
        $gateway['url'].'ZAutomation/api/v1/devices', array(), array('headers' => array(
          'Cookie: ZWAYSession='.(self::$apiState['sid']),
        )));
      $data = json_decode($req['body'], true);
      if($data['code'] == '401' && $autoLogin)
      {
        $data['login-required'] = 'yes';
        $data['login-data'] = self::DeviceLogin($gateway);
        if($data['login-data']['auth']['data']['sid']) # looks good, try again
          return(self::RetrieveDeviceData($gateway, false));
      }
      file_put_contents('data/zwave-'.$gateway['id'].'.devicedata.json', 
        json_encode($data['data'] ? $data['data'] : $data));
      return($data['data'] ? $data['data'] : $data);
    }
    
    static function DeviceDataToDatabase($data, $metaData)
    {
      foreach($data['vdev'] as $vdid => $dev) if(!$metaData['devices'][$vdid]['hidden'])
      {
        $m = $metaData['devices'][$vdid];
        foreach($dev as $sk => $subdev) 
        {
          $info = H2Zwave::getDeviceInfo($subdev);
          $ds = db()->getDS('devices', $vdid.':'.$sk, 'd_id');  
          if($subdev['metrics']['level'] != $ds['d_state'])
          {
            $ds['d_statuschanged'] = time();
            if($ds['d_key'] == 0)
              $ds['d_added'] = time();
            foreach(array(
              'd_bus' => 'ZW',
              'd_type' => first($subdev['probeType'], $subdev['deviceType']),
              'd_room' => $m['location'],
              'd_name' => first($m['name'], $vdid.':'.$sk),
              'd_id' => $vdid.':'.$sk,
              'd_visible' => $m['hidden'] ? 'N' : 'Y',
              'd_state' => $subdev['metrics']['level'],
              ) as $k => $v)
              $ds[$k] = $v;
            db()->commit('devices', $ds);
          }
        }
      }
    }
    
    static function GetDeviceMetaData($gateway)
    {
      $rawData = json_decode(file_get_contents('config/zwave-'.$gateway['id'].'.metadata.json'), true);
      if(!$rawData)
        $rawData = array('error' => 'file not found', 'error-info' => 'config/zwave-'.$gateway['id'].'.metadata.json');
      return($rawData);
    }
    
    static function GetDeviceData($gateway)
    {
      $rawData = json_decode(file_get_contents('data/zwave-'.$gateway['id'].'.devicedata.json'), true);
      foreach($rawData['devices'] as $dev)
      {
        $vdex = explode('_', nibble('-', $dev['id']));
        $vdid = $vdex[sizeof($vdex)-1];
        if($vdid != 'Int')
        {
          $group = $dev['id'];
          $rawData['vdev'][$vdid][$group] = $dev;
        }
      }
      return($rawData);
    }
    
  }