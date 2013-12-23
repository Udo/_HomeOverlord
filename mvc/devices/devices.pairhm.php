<?= $this->_getSubmenu2() ?>
<h1><?= l10n($_REQUEST['controller'].'.'.$_REQUEST['action']) ?></h1>
<div class="description">

</div>
<div id="modeindicator">
  <input type="button" value="Start" onclick="startPairingMode();"> 
  Pairing Mode <span id="modeindicatorldr" style="display:none;"><img src="icons/ajax-loader.gif" height="10" align="absmiddle"></span>
</div>

<div id="pairingmodemsg" style="display:none">
  Pairing active for <span id="timervalue">0</span> seconds...
</div>

<br/><br/>
<h2>Events</h2>
<div id="log" class="pane" style="min-height: 60px">

</div>


<br/><br/>
<h2>After Installation</h2>
<div>
  &gt; <a href="<?= actionUrl('pair', 'devices') ?>">pairing finished</a>
</div>

<script>

document.modeIndicatorOriginal = $('#modeindicator').html();

startPairingMode = function() {
  $('#modeindicatorldr').fadeIn('normal');
  $.post('?', { action : 'ajax_pairhmstart', controller : 'devices' }, function(data) {
    $('#modeindicator').html($('#pairingmodemsg').html());
    document.timerValue = data;
    });
}

setInterval(function() {
  if(document.timerValue == 1)
    $('#modeindicator').html(document.modeIndicatorOriginal);
  if(document.timerValue > 0)
    document.timerValue--;
  $('#timervalue').text(document.timerValue);
  }, 1000);

wsConnect = function() {
  
  ws = new WebSocket('ws://<?= cfg('service/server').':'.cfg('service/wsport') ?>/ws');
  ws.onopen = function() {
    console.log('web socket connected');
  };
  ws.onmessage = function(evt) {
  
    console.log('ws: '+evt.data);
    var data = JSON.parse(evt.data);
    
    // single device status update
    if(data.type == 'busmessage') {
      var msg = data.data;
      $('#log').append('<div>'+msg.type+'-'+msg.device+' '+msg.param+'='+msg.value+'</div>');
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

wsConnect();

</script>
