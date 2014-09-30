<div style="margin:-12px;margin-top:-24px;float:left;width:50px;padding:8px;" class="shadedBackground">

<?
  $modes = $nv->get('pref/modes');
  $currentState = $nv->get('state/current');
  
  $defaultIcons = array(
    'Alarm' => 'exclamation-triangle',
    'At Home' => 'home',
    'Away' => 'suitcase',
    'No Auto' => 'clock-o',
    'Lockdown' => 'lock',
    'Night' => 'moon-o',
    'Default' => 'circle-o',
    );
    
  $ctr = 0; $modeIndex = array();
  foreach($modes as $m)
  {
    $modeIndex[$m] = $ctr;
    $icon = first($defaultIcons[$m], $defaultIcons['Default']);
    ?><div style="text-align:center;margin-bottom: 4px;line-height:100%;"><i
      id="mode-<?= $ctr++ ?>" 
      onclick="setHomeMode('<?= $m ?>')"
      style="cursor:pointer;" class="modeIcon fa fa-2x fa-<?= $icon ?> <?
      if($currentState['mode'] == $m) print('onColor');
      ?>"></i>
    <br/><span style="font-size: 70%;">
      <?= htmlspecialchars($m) ?>
    </span></div><?
  }
?>

</div>

<script>

var modeIndex = <?= json_encode($modeIndex) ?>;

function setHomeMode(md) {
  $.post('<?= actionUrl('ajax_setmode', 'devices') ?>',
    { mode : md }, function(data) { console.log('mode set '+md + ' / ' + data); });
}

messageHandlers['modeSwitch'] = function(data) {
  $('.modeIcon').removeClass('onColor');
  $('#mode-'+modeIndex[data.currentMode]).addClass('onColor');
}

</script>