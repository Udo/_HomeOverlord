<?php

class HMBlinds extends H2Actuator
{

  function __construct()
  {
    $this->fullyClosed = 0.5;
    $this->simpleType = 'MultiState';
  }

  function listStates() 
  {
    return(array('open', '75% open', '50% open', '25% open', 'closed'));
  }

  function getState()
  {
    $value = $this->deviceDS['d_state'];
    if($value >= $this->fullyClosed)
      return('closed');
    if($value >= $this->fullyClosed * 0.75)
      return('25% open');
    if($value >= $this->fullyClosed * 0.50)
      return('50% open');
    if($value >= $this->fullyClosed * 0.25)
      return('75% open');
    return('open');    
  }
  
  function setState($value, $reason = 'unknown')
  {
    $result = 0;
    switch($value)
    {
      case('closed'): {
        $result = $this->fullyClosed * 1.00;
        break;
      }
      case('25% open'): {
        $result = $this->fullyClosed * 0.75;
        break;
      }
      case('50% open'): {
        $result = $this->fullyClosed * 0.50;
        break;
      }
      case('75% open'): {
        $result = $this->fullyClosed * 0.25;
        break;
      }
      case('open'): {
        $result = 0;
        break;
      }
    }
    print_r(SendHMCommand($this->deviceDS, 'LEVEL', $result, $reason));
    return(true);
  }

}