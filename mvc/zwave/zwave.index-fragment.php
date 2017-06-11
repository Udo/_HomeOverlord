<?

if(!$isEmbedded)
  $GLOBALS['config']['page']['template'] = 'blank';


$gateways = $GLOBALS['config']['zwave']['gateways']; #cfg('zwave/gateways'); <-- fixme: that's odd
#print_r($gateways);

foreach($gateways as $gw)
{
  #H2Zwave::RetrieveDeviceData($gw);  
  $data = H2Zwave::GetDeviceData($gw);
  $metaData = H2Zwave::GetDeviceMetaData($gw);
  H2Zwave::DeviceDataToDatabase($data, $metaData);
?>
  
  <div>
    
    <h2>Z-Wave Gateway: <?= $gw['name'] ?> (upd.: <?= ageToString($data['updateTime']) ?>)</h2>
    
    <?
    
    $deviceItems = array();  
    
    foreach($data['vdev'] as $vdid => $dev) if(!$metaData['devices'][$vdid]['hidden'])
    {
      ob_start();
      $alarmClass = '';
      $updateTime = 0;
      $name = first($metaData['devices'][$vdid]['name'], 'DEV#'.$vdid);
      foreach($dev as $subdev) 
      {
        if($subdev['updateTime'] && $subdev['updateTime'] > $updateTime)
          $updateTime = $subdev['updateTime'];
        if(
          first($subdev['metrics']['probeTitle'], $subdev['metrics']['title']) == 'Smoke Alarm' &&
          strtolower($subdev['metrics']['level']) == 'on')
        {
          $alarmClass = 'smoke-alarm';
        }
        else if(
          first($subdev['metrics']['probeTitle'], $subdev['metrics']['title']) == 'Heat Alarm' &&
          strtolower($subdev['metrics']['level']) == 'on')
        {
          $alarmClass = 'heat-alarm';
        }
        else if(
          first($subdev['metrics']['probeTitle'], $subdev['metrics']['title']) == 'PIR' &&
          strtolower($subdev['metrics']['level']) == 'on')
        {
          $alarmClass = 'motion-alarm';
        }
      }
      ?><div class="device <?= $alarmClass ?>">
        <div class="device-header">
          <a title="<?= ageToString($updateTime) ?>"><?= $name ?></a>
          | <?= first($metaData['devices'][$vdid]['location'], '?') ?>
        </div>
        <div class="device-info">
          <? 
          foreach($dev as $subdev) 
          {
            #print_r($subdev['metrics']['title'].':'.$subdev['metrics']['probeTitle'].' ');
            $info = H2Zwave::getDeviceInfo($subdev);
            ?><a style="font-size: 10px; color: black;" title="<?= $subdev['metrics']['title'] ?>"><?
            if($info['onoff'])
            {
              if($subdev['metrics']['level'] == 'on')
                print('<span style="color:red" class="blinking-icon">');
              else
                print('<span style="color:gray">');
              print(H2Zwave::getSensorName($subdev).'</span> ');
            }
            else
            {
              ?>
              <?= H2Zwave::getSensorName($subdev) ?>
              <?= number_format($subdev['metrics']['level'], 1) ?>
              <?= $subdev['metrics']['scaleTitle'] ?>
              <?
            } 
            ?></a><?
          } 
          ?>
        </div>
      </div><?
      $deviceItems[$name] = ob_get_clean();
    }
    
    ksort($deviceItems);
    print(implode(' ', $deviceItems));
      
    ?>
    
    <!--
    <? print_r($metaData); ?>
    <? print_r($data['vdev']); ?>
    -->
  
  </div>
<?
}
?>
