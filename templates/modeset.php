<div style="margin:-12px;margin-top:-12px;float:left;width:50px;padding:8px;" class="shadedBackground">

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
    
  $ctr = -1; $modeIndex = array();
  foreach($modes as $m)
  {
    $ctr++;
    $modeIndex[$m] = $ctr;
    $icon = first($defaultIcons[$m], $defaultIcons['Default']);
    ?><div class="HOModeIcon <?= $currentState['mode'] == $m ? 'selected onColor' : '' ?>"
      id="mode<?= $ctr ?>" 
      onclick="setHomeMode('<?= $m ?>', <?= $ctr ?>)"
      style="cursor:pointer;"><i class="modeIcon fa fa-2x fa-<?= $icon ?>"></i>
    <br/><span style="font-size: 70%;">
      <?= htmlspecialchars($m) ?>
    </span></div><?
  }
?>

</div>

<script>

var modeIndex = <?= json_encode($modeIndex) ?>;

var controlState = 'collapsed';

function setHomeMode(md, idx) {
  if(controlState == 'collapsed') {
    $('.HOModeIcon').addClass('selected');
    controlState = 'open';
  } else {
    selectIcon(md);
    $.post('<?= actionUrl('ajax_setmode', 'devices') ?>',
      { mode : md }, function(data) { console.log('mode set '+md + ' / ' + data); });
  }
}

messageHandlers['modeSwitch'] = function(data) {
  selectIcon(data.currentMode);
}

selectIcon = function(modeName) {
  console.log('select icon '+modeIndex[modeName]);
  $('.HOModeIcon').removeClass('selected').removeClass('onColor');
  $('#mode'+modeIndex[modeName]).addClass('onColor selected');
  controlState = 'collapsed';
}

</script>

<style>

.HOModeIcon {
  text-align:center;margin-bottom: 4px;line-height:100%;
  width: 99%;
  display: none;
}

.selected {
  display: inline-block;
}

</style>