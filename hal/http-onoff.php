<?php

class HttpOnOff extends H2Actuator
{

  function __construct()
  {
    $this->simpleType = 'OnOff';
  }

  function listStates() 
  {
    return(array('on', 'off'));
  }

  function getState()
  {
    return(
      $this->deviceDS['d_state'] == 1 ? 'on' : 'off'
      );
  }
  
  function setState($value, $reason = 'unknown')
  {
    #SendHMCommand($this->deviceDS, 'STATE', $value == 'on' ? true : false, $reason, $this->config);
    $did = $this->deviceDS['d_id'];
    $addr = nibble('#', $did);
    shell_exec('curl http://'.$addr.'/set?'.$did.'='.($value == 'on' ? 1 : 0));
    recordDeviceStatus($$this->deviceDS, 'STATE', $value == 'on' ? true : false, $reason);
    return(true);
  }

}
