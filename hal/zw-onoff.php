<?php

class ZWOnOff extends H2Actuator
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
    $res = H2Zwave::deviceCommand($this->deviceDS['d_id'], $value == 'on' ? 'on' : 'off');
    #WriteToFile('log/hal.command.log', json_encode($res).chr(10));
    return(true);
  }

}