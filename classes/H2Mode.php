<?php

class H2Mode
{

  function __construct() 
  {
    $this->nv = new H2NVStore();
    $this->currentState = $this->nv->get('state/current');
    $this->current = &$currentState['mode'];
  }
  
  function set($mode)
  {
    $evt = new H2Event();
    $oldMode = $this->currentState['mode'];
    $this->currentState['mode'] = $mode;
    $this->nv->set('state/current', $this->currentState);
    $evt->triggerEventByName('MODE-'.$oldMode.'-OFF');
    $evt->triggerEventByName('MODE-'.$mode.'-ON');
    broadCast(array('type' => 'modeSwitch', 'currentMode' => $mode, 'oldMode' => $oldMode));
  }

}