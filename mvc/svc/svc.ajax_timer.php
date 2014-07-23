<?

WriteToFile('log/switch.log', 'timer came back: '.$_POST['data'].chr(10));

$timerConfig = json_decode($_POST['data'], true);

$device = o(db)->getDS('devices', $timerConfig['key']);

if(sizeof($device) > 0)
{
  $dcfg = json_decode($device['d_config'], true);
  $tmr = $dcfg[$timerConfig['trigger']];
  if($tmr)
  {
    WriteToFile('log/switch.log', 'timer trigger found: '.$timerConfig['trigger'].chr(10));
    deviceCommand($timerConfig['key'], $timerConfig['param'], $tmr['value'], first($tmr['stxt'], 'API'));
  }
  else
  {
    WriteToFile('log/switch.log', 'timer trigger NOT found: '.$timerConfig['trigger'].chr(10));
    
  }
}