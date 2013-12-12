<div id="wstate"></div>
<div id="container"><?
foreach($this->devices as $dtype => $dt)  
{  
  ?><div class="smalltext bottomborder"><?= htmlspecialchars($dtype) ?></div><?
  foreach($dt as $ds)
  {
    $iconFile = 'icons/'.strtolower($ds['d_type']).'.png';
    if(!file_exists($iconFile))
      $iconFile = 'icons/default.png';
    if($ds['d_type'] == 'Light' || $ds['d_type'] == 'IT') 
    {
      ?><div class="device_line highlightable" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>"
        onclick="toggleDevice(<?= $ds['d_key'] ?>);">
        <div id="icon_<?= $ds['d_key'] ?>" class="device_icon2 state_<?= $ds['d_state'] ?>" 
          data-state="<?= $ds['d_state'] ?>"
          data-onclick="toggleDevice(<?= $ds['d_key'] ?>);"></div>
          <div><?= so($ds['d_name']) ?> </div>
          <div class="smalltext"><span id="indicator_<?= $ds['d_key'] ?>"></span> 
            <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; </div>
      </div><?  
    }
    else if($ds['d_type'] == 'Blinds')
    {
      $closedValue = 0.5;
      $options = array(array('value' => 0, 'caption' => 'open'));
      for($oc = 0; $oc < 3; $oc++)
      {
        $options[] = array('value' => (($closedValue/4)*($oc+1)), 'caption' => ((100/4)*(3-$oc)).'% open ');
      }
      $options[] = array('value' => $closedValue, 'caption' => 'closed');
      ?><div class="device_line" data-type="<?= $ds['d_type'] ?>" id="dvc_<?= $ds['d_key'] ?>">
        <div id="icon_<?= $ds['d_key'] ?>"  
          data-state="<?= $ds['d_state'] ?>"
          style="background-image: url(<?= $iconFile ?>);background-size: 100%;opacity:0.7;" class="device_icon2"></div>
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
            <span class="smalltext" id="stxt_<?= $ds['d_key'] ?>"><?= so($ds['d_statustext']) ?></span>&nbsp; </div>
      </div><?  
    }
  }
}
?></div><script>

//$('#container').masonry();

wsConnection = null;

setDeviceState = function(deviceKey, paramName, paramValue) {

  console.log('setDeviceState '+deviceKey +' '+ paramName +' '+ paramValue);

  $('#indicator_'+deviceKey).html('<img src="icons/ajax-loader.gif" height="8"/>');
  
  $.post('./', 
    { controller : 'devices', action : 'ajax_switch', p : paramName, key : deviceKey, v : paramValue, by : 'UI' }, 
    function(data) { 
    
      $('#indicator_'+deviceKey).text(' ');
    
    });

}

toggleDevice = function(deviceId) {

  var icon = $('#icon_'+deviceId);
  var state = icon.attr('data-state');

  var newState = state == 1 ? 0 : 1;
  $('#indicator_'+deviceId).html('<img src="icons/ajax-loader.gif" height="8"/>');
  
  $.post('./', 
    { controller : 'devices', action : 'ajax_switch', key : deviceId, v : newState, by : 'UI' }, 
    function(data) { 
    
      icon.removeClass('state_'+state).addClass('state_'+newState);
      icon.attr('data-state', newState);
        
      $('#indicator_'+deviceId).text(' ');
    
    });

}

function updateDeviceLight(dline, data) {
  if(data.value == 'true') data.value = 1;
  if(data.value == 'false') data.value = 0;
  var icon = $('#icon_'+data.key);
  var state = icon.attr('data-state');
  icon.removeClass('state_'+state).addClass('state_'+data.value);
  icon.attr('data-state', data.value);
}

function updateDeviceBlinds(dline, data) {
  $('#dvc_'+data.key+' select').val(data.value);
}

updateDeviceWidget = function(data) {

  var deviceLine = $('#dvc_'+data.key);
  var deviceType = deviceLine.data('type');
  var updateHandler = 'updateDevice'+deviceType+'(deviceLine, data);';
  $('#indicator_'+data.key).html('<span class="fadeout">update</span>');
  $('.fadeout').fadeOut('normal');
  if(data.stxt)
    $('#stxt_'+data.key).text(data.stxt);
  
  eval(updateHandler);  

}

displayMessage = function(data) {
  
  $('#msgheader').html('<div id="rtmsg" class="messageline">'+data.text+'</div>');
  $('#rtmsg').fadeIn('normal');
  
  if(!data.displayTime)
    data.displayTime = 60;
  
  setTimeout(function() {
    $('#rtmsg').fadeOut('normal');
    }, 1000*data.displayTime);
  
}

wsConnect = function() {
  
  ws = new WebSocket('ws://<?= cfg('service/server').':'.cfg('service/wsport') ?>/ws');
  ws.onopen = function() {
    console.log('web socket connected');
  };
  ws.onmessage = function(evt) {
  
    console.log('ws: '+evt.data);
    var data = JSON.parse(evt.data);
    
    // single device status update
    if(data.type == 'devicestatus') {
      updateDeviceWidget(data);
    }    
    else if(data.type == 'reload') {
      window.location.reload(true);
    }    
    else if(data.type == 'message') {
      displayMessage(data);
    }    
    
    
  };
  ws.onclose = function() {
    console.log('web socket connection lost');
    $('#lefthdr').html('[connection lost]');
    wsReconnect();  
  };
  ws.onerror = function(evt) {
    console.log('web socket error');
  };

};

wsReconnect = function() {
  setTimeout(function() {  wsConnect(); }, 1000);
}

leadingZero = function(v) {
  if(v < 10) v = '0'+v;
  return(v);
}

weatherInfoState = 0;
weatherInfo = [];

weatherAndTimeUpdate = function() {

  if(weatherInfo.length == 0) return;
  
  weatherInfoState++;
  if(weatherInfoState >= weatherInfo.length) weatherInfoState = 0;
  
  var d = new Date();
  $('#lefthdr').html(leadingZero(d.getHours())+':'+leadingZero(d.getMinutes())+
    '   <img src="'+document.weather.icon+'" height="28" align="absmiddle" style="margin-bottom: -8px;margin-top: -8px;"/> '+
    weatherInfo[weatherInfoState]
    );

}

wsReconnect();

document.weather = <?= file_get_contents('data/weather.json') ?>;
weatherAndTimeUpdate();

updateWeatherInfo = function() {
  $.get('data/weather.json?v='+Math.random(), {}, function(data) {
    
    document.weather = data;
    weatherInfo = [
      document.weather.tecur+'°C | '+document.weather.wscur+'km/h',
      document.weather.description,
      ];
      
    var dt = new Date();
    var currentMinutes = dt.getMinutes()+dt.getHours()*60;
    if(currentMinutes > sunSet - 120 && currentMinutes < sunSet + 120)
    {
      if(currentMinutes > sunSet)
        weatherInfo.push('sunset '+(currentMinutes-sunSet)+' min ago');
      else
        weatherInfo.push('sunset in '+(sunSet-currentMinutes)+'min');
    }
    else if(currentMinutes > sunRise - 120 && currentMinutes < sunRise + 120)
    {
      if(currentMinutes > sunRise)
        weatherInfo.push('sunrise '+(currentMinutes-sunRise)+' min ago');
      else
        weatherInfo.push('sunrise in '+(sunRise-currentMinutes)+'min');
    }

  if(currentSunState == 'night')
    weatherInfo.push('moon '+document.weather.moonlight+'% '+document.weather.moonstage);

  }, 'json');
  };

setInterval(weatherAndTimeUpdate, 3000);
  
setInterval(updateWeatherInfo, 60*1000);

updateWeatherInfo();

</script>