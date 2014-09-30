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
<div id="log" class="pane" style="height: 300px;overflow:auto;padding: 8px;">

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

messageHandlers.busmessage = function(data) {
  var msg = data.data;
  $('#log')
    .append('<div>'+msg.type+'-'+msg.device+' '+msg.param+'='+msg.value+'</div>')
    .scrollTop($("#log")[0].scrollHeight);
}

</script>
