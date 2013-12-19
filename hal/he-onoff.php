<?php

class HEOnOff extends H2Actuator
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
    SendHECommand($this->deviceDS, 'STATE', $value == 'on' ? 1 : 0, $reason);
    return(true);
  }

}