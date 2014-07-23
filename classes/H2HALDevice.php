<?php

// hardware abstraction layer

class H2HALDevice
{

  function __construct($deviceKey) 
  {
    $this->deviceDS = o(db)->getDS('devices', $deviceKey);
    if(sizeof($this->deviceDS) == 0)
      $this->deviceDS = o(db)->getDS('devices', $deviceKey, 'd_alias');
    $this->type = $this->deviceDS['d_type'];
    $this->bus = $this->deviceDS['d_bus'];
    $this->address = $this->deviceDS['d_id'];
    $this->config = json_decode($this->deviceDS['d_config'], true);
    $this->deviceHandlerClass = $this->bus.cfg('hal/'.$this->type);
    $this->deviceHandlerFile = strtolower('hal/'.$this->bus.'-'.cfg('hal/'.$this->type).'.php');
    if(!class_exists($this->deviceHandlerClass))
    { 
      if(file_exists($this->deviceHandlerFile))
      {
        $dh = $this->deviceHandlerClass;
        include($this->deviceHandlerFile);
        $this->handler = new $dh($this);
        $this->handler->deviceDS = &$this->deviceDS;
        $this->handler->config = &$this->config;
        $this->states = $this->handler->listStates();
      }
      else
      {
        logError('Device HAL class not found: '.$this->deviceHandlerClass.', looking for file: '.$this->deviceHandlerFile);
      }
    }
  }
  
  function save()
  {
    if(sizeof($this->deviceDS) > 0)
      o(db)->commit('devices', $this->deviceDS);
  }
  
  function state($value = null, $reason = 'unknown')
  {
    if($value != null)
    {
      return($this->handler->setState($value, $reason));
    }
    else
    {
      return($this->handler->getState());
    }
  }
  
  function dparam($value)
  {
    $key = CutSegment('=', $value);
    $this->deviceDS['d_'.$key] = $value;
    $this->save();
    broadcast(array('type' => 'dparam_'.$key, 'device' => $this->deviceDS['d_key'], 'value' => $value));
  }
  
}