<?php

class GPIOBlinds extends H2Actuator
{

  function __construct()
  {
    $this->simpleType = 'MultiState';
  }

  function listStates() 
  {
    return(array('open', 'closed'));
  }
  
  function getState()
  {
    return($this->deviceDS['d_state']);
  }
  
  function setState($value)
  {
    // pins have to be specified in order UP:STOP:DOWN
    $this->pins = explode(':', $this->deviceDS['d_id']);
    if($value == 'closed') $pinIdx = 2; else $pinIdx = 0;
    SendGPIOCommand($this->deviceDS, $this->pins[$pinIdx], 1, $reason);
    return(true);
  }

}