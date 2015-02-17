<?

WriteToFile('log/switch.log', 'timer came back: '.$_POST['data'].chr(10));

$timerConfig = json_decode($_POST['data'], true);

$device = getDeviceDS($timerConfig['key']);

if(sizeof($device) > 0)
{
  $dcfg = json_decode($device['d_config'], true);
  $tmr = $dcfg[$timerConfig['trigger']];
  if($tmr)
  {
    $GLOBALS['command-mode'] = 'trigger';
    deviceCommand($timerConfig['key'], $timerConfig['param'], $tmr['value'], first($tmr['stxt'], 'API'));
  }
  else
  {
    WriteToFile('log/error.log', 'timer trigger not found: '.$timerConfig['trigger'].chr(10));
  }
}