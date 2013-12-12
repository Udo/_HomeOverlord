<?= $this->_getSubmenu2() ?>
<?

$this->devices = array();
foreach(o(db)->get('SELECT * FROM devices 
  ORDER BY d_type, d_key DESC') as $dds)
{
  $this->devices[$dds['d_bus'].':'.$dds['d_id']] = $dds;
}
$this->hmDevices = HMRPC('listDevices', array());
foreach($this->hmDevices as $h)
{
  switch($h['TYPE'])
  {
    case('KEY'):
    {
      if(!isset($this->devices['HM:'.$h['ADDRESS']]))
      {
        // unknown device, make new dataset
        $dds = array(
          'd_bus' => 'HM',
          'd_type' => 'Key',
          'd_room' => 'unknown',
          'd_name' => 'New Key '.date('Y-m-d H:i:s'),
          'd_id' => $h['ADDRESS'],
          );
        $dds['d_key'] = o(db)->commit('devices', $dds);
        $this->devices['HM:'.$h['ADDRESS']] = $dds;
      }
      $this->devices['HM:'.$h['ADDRESS']]['info'] = $h;
      break;
    }
    case('BLIND'):
    case('SWITCH'):
    {
      if(!isset($this->devices['HM:'.$h['ADDRESS']]))
      {
        // unknown device, make new dataset
        $dds = array(
          'd_bus' => 'HM',
          'd_type' => $h['TYPE'] == 'BLIND' ? 'Blinds' : 'Light',
          'd_room' => 'unknown',
          'd_name' => 'New '.($h['TYPE'] == 'BLIND' ? 'Blinds' : 'Switch').' '.date('Y-m-d H:i:s'),
          'd_id' => $h['ADDRESS'],
          );
        $dds['d_key'] = o(db)->commit('devices', $dds);
        $this->devices['HM:'.$h['ADDRESS']] = $dds;
      }
      $this->devices['HM:'.$h['ADDRESS']]['info'] = $h;
      break;
    }
    default:
    {
      if(!isset($this->devices['HM:'.$h['ADDRESS']]))
      {
        // unknown device, make new dataset
        $dds = array(
          'd_bus' => 'HM',
          'd_type' => $h['TYPE'],
          'd_room' => 'unknown',
          'd_name' => 'New '.$h['TYPE'].' '.date('Y-m-d H:i:s'),
          'd_id' => $h['ADDRESS'],
          );
        $dds['d_key'] = o(db)->commit('devices', $dds);
        $this->devices['HM:'.$h['ADDRESS']] = $dds;
      }
      $this->devices['HM:'.$h['ADDRESS']]['info'] = $h;
      break;
    }
  }      
}    

foreach($this->devices as $d)
{
  $di = array();
  foreach($d as $k => $v)
    $di[] = $k.'='.$v;
  
  ?><div><?= implode(', ', $di) ?></div><?
}

?>