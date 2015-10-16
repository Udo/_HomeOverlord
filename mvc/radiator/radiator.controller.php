<?php

class RadiatorController extends H2Controller
{
  function __init()
  {
    $this->access('local,internal,auth');
    $this->nv = new H2NVStore();
  }

  function index()
  {
    
  }
  
  function getSettings($myId)
  {
    if(!$myId) $myId = 'general';
    $this->radiatorId = $myId;
    $this->radiatorSettings = $this->nv->get('radiators');
    if(!$this->radiatorSettings[$myId])
      $this->radiatorSettings[$myId] = array();
    if(!$this->radiatorSettings[$myId])
      $this->radiatorSettings[$myId] = array(
        'url' => '?/radiator/empty',
      );
    $this->radiator = &$this->radiatorSettings[$myId];
    return($this->radiator);
  }
  
  function saveSettings()
  {
    $this->nv->set('radiators', $this->radiatorSettings);
  }
  
  function configToText()
  {
    if(!$this->radiatorSettings) $this->getSettings();
    $lines = array();
    foreach($this->radiatorSettings as $k => $v)
    {
      foreach($v as $pn => $pv)
      {
        $lines[] = $k.'.'.$pn.'='.$pv;
      }
    }
    return(implode(chr(10), $lines));
  }
  
  function textToConfig($text, $cfg = array())
  {
    $lines = explode(chr(10), $text);
    foreach($lines as $value)
    {
      $value = trim($value);
      if($value != '')
      {
        $key = CutSegment('=', $value);
        $radiatorName = CutSegment('.', $key);
        $cfg[$radiatorName][$key] = $value;
      }
    }    
    ksort($cfg);
    return($cfg);
  }
  
  function broadcastUpdate()
  {
    foreach($this->radiatorSettings as $k => $v)
    {
      broadcast(array_merge(array('type' => 'radiator', 'id' => $k), $v));
    }
  }
  
}

