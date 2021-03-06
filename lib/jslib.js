wsConnection = null;
showMessages = false;

setDeviceState = function(deviceKey, paramName, paramValue) {

  //console.log('setDeviceState '+deviceKey +' '+ paramName +' '+ paramValue);

  $('#indicator_'+deviceKey).html('<i class="fa fa-refresh fa-1x"></i> ');
  
  $.post('./', 
    { controller : 'devices', action : 'ajax_switch', p : paramName, key : deviceKey, v : paramValue, by : 'UI' }, 
    function(data) { 
    
      $('#indicator_'+deviceKey).text(' ');
    
    });

}

HALCommand = function(deviceKey, call, command) {
  $('#indicator_'+deviceKey).html('<i class="fa fa-refresh fa-1x"></i> ');
  
  $.post('./', 
    { controller : 'devices', action : 'ajax_halcommand', key : deviceKey, call : call, command : command, by : 'UI' }, 
    function(data) { 
    
      $('#indicator_'+deviceKey).text(' ');
    
    });
}

toggleDevice = function(deviceId) {

  var icon = $('#icon_'+deviceId);
  var state = icon.attr('data-state');

  var newState = state == 1 ? 0 : 1;
  $('#indicator_'+deviceId).html('<i class="fa fa-refresh fa-1x"></i> ');
  
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

updateDeviceOnOff = updateDeviceLight;

function updateDeviceBlinds(dline, data) {
  $('#dvc_'+data.key+' select').val(data.value);
}

updateDevicenull = function(data) {
  //console.log(data);
}

updateDeviceWidget = function(data) {

  var deviceLine = $('#dvc_'+data.key);
  var deviceType = deviceLine.data('type');
  var updateHandler = 'if (updateDevice'+deviceType+') updateDevice'+deviceType+'(deviceLine, data);';
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

busDataSubscribers = {};

messageHandlers = {
  timerState : function(data) { $('#indicator_'+data.deviceKey).text(data.countDown > 0 ? data.countDown+'sec...' : ''); },
  devicestatus : function(data) { updateDeviceWidget(data); },
  reload : function(data) { window.location.reload(true); },
  message : function(data) { displayMessage(data); },
  busmessage : function(msg) {  
    if(busDataSubscribers[msg.data.device]) 
      busDataSubscribers[msg.data.device](msg.data);
    if(busDataSubscribers['key:'+msg.data.key]) 
      busDataSubscribers['key:'+msg.data.key](msg.data);
    if(busDataSubscribers['param:'+msg.data.param]) 
      busDataSubscribers['param:'+msg.data.param](msg.data);
    if(msg.data.key) 
      updateDeviceWidget(msg.data);  
  },
  dparam_auto : function(data) { 
    var indicator = $('#dvc_'+data.device+' > .dparam_auto');
    indicator.text(data.value).removeClass('green').removeClass('red'); 
    indicator.addClass(data.value == 'A' ? 'green' : 'red');
    },
  };
  
wsConnect = function() {

  if(wsConnection) return;

  ws = new WebSocket('ws://<?= $_SERVER['HTTP_HOST'] ?>/ws/');
  wsConnection = ws;
  ws.onopen = function() {
    console.log('web socket connected');
  };
  ws.onmessage = function(evt) {
  
    var data = JSON.parse(evt.data);
    if(messageHandlers[data.type]) 
       messageHandlers[data.type](data);
       
    if(showMessages)
      console.log('message', evt.data);
    
  };
  ws.onclose = function() {
    //console.log('web socket connection lost');
    $('#lefthdr').html('[connection lost]');
    wsReconnect();  
  };
  ws.onerror = function(evt) {
    console.log('web socket error', evt);
  };

};

wsReconnect = function() {
  setTimeout(function() {  wsConnect(); }, 1000);
  //window.location.reload(true);
}

var elementStore = {};
EditThermostat = function(tid) {
  var thermoControl = $('#dvc_'+tid);
  elementStore['dvc_'+tid] = thermoControl.html();
  thermoControl.html('<select><option>off</option></select>');
}