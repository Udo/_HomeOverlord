<?php


class H2DeviceRenderer
{
  function __construct() 
  {
    $this->serviceFlags = getServiceFlags();
  }

  function display($ds)
  {
    $renderType = cfg('deviceTypeAliases/'.$ds['d_type']);
    if(!$renderType) $renderType = $ds['d_type'];

    $id = $ds['d_id'];
    $idRoot = CutSegment(':', $id);
    $ds['flags'] = $this->serviceFlags[$idRoot];
    $ds['statusIcons'] = array();
    if(is_array($this->serviceFlags[$idRoot])) foreach($this->serviceFlags[$idRoot] as $f) 
    {
      if($f[1] == 'LOWBAT') $ds['statusIcons'][] = '<i class="fa fa-laptop_battery"></i>';
      if($f[1] == 'UNREACH') $ds['statusIcons'][] = '<i class="fa fa-exclamation-circle"></i>';
      if($f[1] == 'STICKY_UNREACH') $ds['statusIcons'][] = '<i class="fa fa-exclamation-triangle"></i>';
    }
    $ds['statusIconStr'] = implode(' ', $ds['statusIcons']);

    $renderFunc = 'display'.$renderType;
    $renderFuncBus = $renderFunc.$ds['d_bus'];
    if(method_exists($this, $renderFuncBus))
      $this->{$renderFuncBus}($ds);
    else if(method_exists($this, $renderFunc))
      $this->{$renderFunc}($ds);
    #else
    #  print('<div class="device_line">'.$ds['d_type'].'</div>');
  } 
  
  function autoConfig($ds)
  {
    return('<div class="leftblock knub '.($ds['d_auto'] == 'A' ? 'green' : 'red').' dparam_auto"
      onclick="HALCommand('.$ds['d_key'].', \'dparam\', \'auto=\'+($(this).text() == \'A\' ? \'M\' : \'A\'));">'.$ds['d_auto'].'</div>');
  }
  
  function displayThermostat($ds)
  {
    $thermState = getExtendedDeviceState($ds['d_id']);
    ?>
      <div class="device_line" 
          data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">

        <span 
          class="asCharacter state_<?= $ds['d_state'] ?>" 
          data-state="<?= $ds['d_state'] ?>"
          style="text-align:center;float:left;width:84px;">
          <div  id="temp_<?= $ds['d_key'] ?>"><?= $thermState['TEMPERATURE'] ?>°C</div>
          <div class="smalltext" id="settemp_<?= $ds['d_key'] ?>"><?= $thermState['SET_TEMPERATURE'] ?>°C</div>
        </span>
        
        <div class="device_line_text">
          <div><select onchange="var vl = $(this).val(); if(vl != -1) setDeviceState('<?= $ds['d_id'] ?>:2', 'SET_TEMPERATURE', vl+'.0'); $(this).val(-1);">
            <option selected value="-1"><?= so($ds['d_name']) ?></option>
            <?
            for($tc = 3; $tc <= 30; $tc++) 
            {
              ?><option value="<?= $tc ?>">
                <?= $tc ?> °C
              </option><?
            }
            ?>
            </select></div>
          <div class="smalltext"> 
            <?= $ds['statusIconStr'] ?>
            <span class="smalltext" id="humidity_<?= $ds['d_key'] ?>">Humidity <?= $thermState['HUMIDITY'] ?>%</span>&nbsp; 
            <span class="smalltext" id="indicator_<?= $ds['d_key'] ?>"></span>
          </div>
        </div>
        
      </div>
      <script>
        busDataSubscribers['<?= $ds['d_id'] ?>:1'] = function(data) {
          if(data.param == 'TEMPERATURE')
            $('#temp_<?= $ds['d_key'] ?>').text(data.value+'°C');
          else if(data.param == 'HUMIDITY')
            $('#humidity_<?= $ds['d_key'] ?>').text('Humidity '+data.value+'%');
        };
        busDataSubscribers['<?= $ds['d_id'] ?>:2'] = function(data) {
          //console.log('UPD', data);
          if(data.param == 'ACTUAL_TEMPERATURE')
            $('#temp_<?= $ds['d_key'] ?>').text(data.value+'°C');
          if(data.param == 'SET_TEMPERATURE')
            $('#settemp_<?= $ds['d_key'] ?>').text(data.value+'°C');
        };
      </script>
    <?
  }
  
  function displayCamera($ds)
  {
    ?>
      <div class="device_line" 
          data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">

        <div id="icon_<?= $ds['d_key'] ?>"  
          data-state="<?= $ds['d_state'] ?>"
          style="float:left;width:60px;margin-top:2px;text-align: right;">
          <?= $ds['d_name'] ?>
          <?= stripos($ds['d_name'], 'cam') === false ? 'Cam' : '' ?>
        </div>
        
        <div class="device_line_text">
          <a href="?/cam/single&id=<?= $ds['d_id'] ?>"><img     
            id="camimg_<?= $ds['d_id'] ?>"
            class="cam_minipic"       
            src="data/cam/<?= $ds['d_id'] ?>_mid.jpg"
            height="50"
            /></a>
        </div>          
        
      </div>
      <script>
        (function() {
          var refreshCam = function() {
            $('#camimg_<?= $ds['d_id'] ?>').attr('src', 'data/cam/<?= $ds['d_id'] ?>_mid.jpg?'+Math.random());
            setTimeout(refreshCam, 5000);
          };
          refreshCam();
        })();
      </script>
    <?
  }
  
  function displayLight($ds, $icon = '')
  {
    ?>
      <div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>"
        xonclick="toggleDevice(<?= $ds['d_key'] ?>);">
        <?= $this->autoConfig($ds) ?>

        <i id="icon_<?= $ds['d_key'] ?>" 
          class="asCharacter fa fa-<?= first($icon, $ds['d_icon'], 'lightbulb-o') ?> fa-3x state_<?= $ds['d_state'] ?>" 
          data-state="<?= $ds['d_state'] ?>"
          style="float:left;width:44px;padding-left:16px;"
          onclick="toggleDevice(<?= $ds['d_key'] ?>);">
        </i>
        
        <div class="device_line_text">
          <div><?= so($ds['d_name']) ?> </div>
          <div class="smalltext"> 
            <?= $ds['statusIconStr'] ?>
            <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; 
            <span class="smalltext" id="indicator_<?= $ds['d_key'] ?>"></span>
          </div>
        </div>
        
      </div>
    <?
  }
  
  function displayOnOff($ds)
  {
    $this->displayLight($ds, 'power-off');
  }
  
  function displayBlindsGPIO($ds)
  {
    ?>
    
    <div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">
      <?= $this->autoConfig($ds) ?>

      <div id="icon_<?= $ds['d_key'] ?>"  
        data-state="<?= $ds['d_state'] ?>"
        style="float:left;width:60px;margin-top:2px;">
        <i class="asCharacter fa fa-chevron-circle-up fa-2x clickable state_0" 
          onclick="HALCommand(<?= $ds['d_key'] ?>, 'state', 'open');"></i>
        <i class="asCharacter fa fa-chevron-circle-down fa-2x  clickable state_0" 
          onclick="HALCommand(<?= $ds['d_key'] ?>, 'state', 'closed');"></i>
      </div>

      <div class="device_line_text">
        <div>
            <div style="display:inline-block;vertical-align:middle;"><?= so($ds['d_name']) ?> </div>
            
        </div>
        <div class="smalltext">
          <?= $ds['statusIconStr'] ?>
          <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; 
          <span class="smalltext" id="indicator_<?= $ds['d_key'] ?>"></span>
        </div>
      </div>
    
    </div>
    
    <?    
  }

  function displayBlinds($ds)
  {
    $closedValue = 0.5;
    $options = array(array('value' => 0, 'caption' => 'open'));
    for($oc = 0; $oc < 3; $oc++)
      $options[] = array('value' => (($closedValue/4)*($oc+1)), 'caption' => ((100/4)*(3-$oc)).'% open ');
    $options[] = array('value' => $closedValue, 'caption' => 'closed');
    ?>
    
    <div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">
      <?= $this->autoConfig($ds) ?>

      <div id="icon_<?= $ds['d_key'] ?>"  
        data-state="<?= $ds['d_state'] ?>"
        style="float:left;width:60px;margin-top:2px;">
        <i class="asCharacter fa fa-chevron-circle-up fa-2x clickable state_0" 
          onclick="setDeviceState(<?= $ds['d_key'] ?>, 'LEVEL', 0);"></i>
        <i class="asCharacter fa fa-chevron-circle-down fa-2x  clickable state_0" 
          onclick="setDeviceState(<?= $ds['d_key'] ?>, 'LEVEL', <?= $closedValue ?>);"></i>
      </div>

      <div class="device_line_text">
        <div>
            <select onchange="setDeviceState(<?= $ds['d_key'] ?>, 'LEVEL', $(this).val());">
            <?
            foreach($options as $o) 
            {
              ?><option <?= $ds['d_state'] == $o['value'] ? 'selected' : '' ?> value="<?= $o['value'] ?>">
                <?= so($ds['d_name']) ?> <?= $o['caption'] ?>
              </option><?
            }
            ?>
            </select>
        </div>
        <div class="smalltext">
          <?= $ds['statusIconStr'] ?>
          <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; 
          <span class="smalltext" id="indicator_<?= $ds['d_key'] ?>"></span>
        </div>
      </div>
    
    </div>
    
    <?    
  }
  

}