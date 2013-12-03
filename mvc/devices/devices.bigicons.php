<table width="100%" style="margin:-8px"><?
foreach($this->devices as $dtype => $dt)  
{  
  ?><tr><td style="background:#012;">
    <div style="padding: 8px;">
      <?= $dtype ?>
    </div>
  </td></tr><tr><td style=""><?
  foreach($dt as $ds)
  {
    $iconFile = 'icons/'.strtolower($ds['d_type']).'.png';
    if(!file_exists($iconFile))
      $iconFile = 'icons/default.png';
    ?><div id="icon_<?= $ds['d_key'] ?>" class="device_icon state_<?= $ds['d_state'] ?>" 
      data-state="<?= $ds['d_state'] ?>"
      onclick="toggleDevice(<?= $ds['d_key'] ?>);">
    
      <img src="<?= $iconFile ?>" height="70"><br/>
      <?= so($ds['d_name']) ?><br/>
      <div class="smalltext"><?= so($ds['d_room']) ?></div>
      <div class="smalltext" id="indicator_<?= $ds['d_key'] ?>"><!--<?= so($ds['d_state']) ?>--></div>
    
    </div><?  
  }
  ?></td></tr><?
}
?></table><script>

toggleDevice = function(deviceId) {

  var icon = $('#icon_'+deviceId);
  var state = icon.attr('data-state');

  var newState = state == 1 ? 0 : 1;
  $('#indicator_'+deviceId).html('<img src="icons/ajax-loader.gif" width="100"/>');
  
  $.post('./', 
    { controller : 'devices', action : 'ajax_switch', key : deviceId, v : newState }, 
    function(data) { 
    
      icon.removeClass('state_'+state).addClass('state_'+newState);
      icon.attr('data-state', newState);
        
      $('#indicator_'+deviceId).text(' ');
    
    });

}

leadingZero = function(v) {
  if(v < 10) v = '0'+v;
  return(v);
}

document.weather = <?= file_get_contents('data/weather.json') ?>;

setInterval(function() {
  var d = new Date();
  $('#lefthdr').html(leadingZero(d.getHours())+':'+leadingZero(d.getMinutes())+
    ' | '+document.weather.tecur+'°C'+
    ' | '+document.weather.wscur+'km/h'+
    ' | '+document.weather.pccur+'mm'
    );
  }, 1000);
  
setInterval(function() {
  $.get('data/weather.json?v='+Math.random(), {}, function(data) {
    document.weather = data;
  }, 'json');
  }, 10000);

</script>