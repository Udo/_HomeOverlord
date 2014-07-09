<?php

class DeviceRendererModel
{
  function display($ds)
  {
    $renderFunc = 'display'.$ds['d_type'];
    $renderFuncBus = $renderFunc.$ds['d_bus'];
    $iconFile = 'icons/'.strtolower($ds['d_type']).'.png';
    if(!file_exists($iconFile))
      $iconFile = 'icons/default.png';
    if(method_exists($this, $renderFuncBus))
      $this->{$renderFuncBus}($ds, $iconFile);
    else if(method_exists($this, $renderFunc))
      $this->{$renderFunc}($ds, $iconFile);
    #else
    #  print('<div class="device_line">'.$ds['d_type'].'</div>');
  } 
  
  function displayLight($ds, $iconFile)
  {
    ?>
      <div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>"
        xonclick="toggleDevice(<?= $ds['d_key'] ?>);">
        
        <div class="leftblock knub green"><?= $ds['d_auto'] ?></div>

        <div id="icon_<?= $ds['d_key'] ?>" class="device_icon2 highlightable state_<?= $ds['d_state'] ?>" 
          data-state="<?= $ds['d_state'] ?>"
          onclick="toggleDevice(<?= $ds['d_key'] ?>);">
        </div>
        
        <div class="device_line_text">
          <div><?= so($ds['d_name']) ?> </div>
          <div class="smalltext"><span id="indicator_<?= $ds['d_key'] ?>"></span> 
            <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; 
          </div>
        </div>
        
      </div>
    <?
  }
  
  function displayIT($ds, $iconFile)
  {
    $this->displayLight($ds, $iconFile);
  }
  
  function displayBlindsGPIO($ds, $iconFile)
  {
    ?>
    
    <div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">
      <div class="leftblock knub green"><?= $ds['d_auto'] ?></div>

      <div id="icon_<?= $ds['d_key'] ?>"  
        data-state="<?= $ds['d_state'] ?>"
        style="float:left;width:60px;margin-top:2px;">
        <img class="upDownArrow" src="icons/1uparrow.png" width="24" height="32"
          onclick="HALCommand(<?= $ds['d_key'] ?>, 'open');"/>
        <img class="upDownArrow" src="icons/1downarrow.png" width="24" height="32"
          onclick="HALCommand(<?= $ds['d_key'] ?>, 'closed');"/>
      </div>

      <div class="device_line_text">
        <div>
            <div style="display:inline-block;vertical-align:middle;"><?= so($ds['d_name']) ?> </div>
            
        </div>
        <div class="smalltext"><span id="indicator_<?= $ds['d_key'] ?>"></span> 
          <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; 
        </div>
      </div>
    
    </div>
    
    <?    
  }

  function displayBlinds($ds, $iconFile)
  {
    $closedValue = 0.5;
    $options = array(array('value' => 0, 'caption' => 'open'));
    for($oc = 0; $oc < 3; $oc++)
      $options[] = array('value' => (($closedValue/4)*($oc+1)), 'caption' => ((100/4)*(3-$oc)).'% open ');
    $options[] = array('value' => $closedValue, 'caption' => 'closed');
    ?>
    
    <div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">
      <div class="leftblock knub green"><?= $ds['d_auto'] ?></div>

      <div id="icon_<?= $ds['d_key'] ?>"  
        data-state="<?= $ds['d_state'] ?>"
        style="float:left;width:60px;margin-top:2px;">
        <img class="upDownArrow" src="icons/1uparrow.png" width="24" height="32"
          onclick="setDeviceState(<?= $ds['d_key'] ?>, 'LEVEL', 0);"/>
        <img class="upDownArrow" src="icons/1downarrow.png" width="24" height="32"
          onclick="setDeviceState(<?= $ds['d_key'] ?>, 'LEVEL', <?= $closedValue ?>);"/>
      </div>

      <div class="device_line_text">
        <div>
            <?= so($ds['d_name']) ?> 
            <select onchange="setDeviceState(<?= $ds['d_key'] ?>, 'LEVEL', $(this).val());">
            <?
            foreach($options as $o) 
            {
              ?><option <?= $ds['d_state'] == $o['value'] ? 'selected' : '' ?> value="<?= $o['value'] ?>"><?= $o['caption'] ?></option><?
            }
            ?>
            </select>
        </div>
        <div class="smalltext"><span id="indicator_<?= $ds['d_key'] ?>"></span> 
          <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; 
        </div>
      </div>
    
    </div>
    
    <?    
  }
  

}