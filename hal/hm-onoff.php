<?php

class HMOnOff extends H2Actuator
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
    SendHMCommand($this->deviceDS, 'STATE', $value == 'on' ? true : false, $reason);
    return(true);
  }

}