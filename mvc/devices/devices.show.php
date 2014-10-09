<?= $this->_getSubmenu2('show') ?>
<?

function getCellEditor($ds, $fname)
{
  $cellId = 'c'.$ds['d_key'].'_'.$fname;
  return('<span class="editable" id="'.$cellId.'" onclick="editCell(\''.$cellId.'\');" data-key="'.$ds['d_key'].'" data-field="'.$fname.'">'.htmlspecialchars($ds[$fname]).'</span>');
}

function getCellCheckbox($ds, $fname)
{
  $cellId = 'c'.$ds['d_key'].'_'.$fname;
  return('<input type="checkbox" value="Y" onchange="saveCheckbox(\''.$cellId.'\')" data-key="'.$ds['d_key'].'" data-field="'.$fname.'" id="'.$cellId.'" '.($ds[$fname] == 'Y' ? 'checked' : '').'/>');
}

$deviceFlags = getServiceFlags();

$deviceIcons = array(
  'default' => 'code-fork',
  'LIGHT' => 'lightbulb-o',
  'BLINDS' => 'arrows-v',
  'MOTION_DETECTOR' => 'child',
  'KEY' => 'keyboard-o',
  'MAINTENANCE' => 'history',
  'HM-TC-IT-WM-W-EU' => 'tasks',
  'THERMALCONTROL_TRANSMIT' => 'tasks',
  'WEATHER_TRANSMIT' => 'tasks',
  );

?>
<table class="devicetable">
<thead>
  <tr>
    <th></th>
    <th>Type</th>
    <th>#</th>
    <th>Vis.</th>
    <th>Address</th>
    <th>Name</th>
    <th>Alias</th>
    <th>Room</th>
  </tr>
</thead><?

  db()->get('UPDATE #devices SET d_added = '.time().' WHERE d_added = 0');

  foreach(o(db)->get('SELECT * FROM devices ORDER BY d_room,d_id') as $ds) 
  {
    $id = $ds['d_id'];
    $did = CutSegment(':', $id);
    $info = '';
    if($deviceFlags[$did])
    {
      $flags = array();
      $img = 'error';
      foreach($deviceFlags[$did] as $f) 
      {
        $flags[] = $f[1];
        if($f[1] == 'LOWBAT') $img = 'laptop_battery';
      }
      $info = '<img src="icons/'.$img.'.png" width="22" style="margin:-4px;" title="'.implode(', ', $flags).'"/>';
    }
    $icon = $deviceIcons[strtoupper($ds['d_type'])];
    if(!$icon) $icon = $deviceIcons['default'];
    $editUrl = actionUrl('edit', 'devices', array('key' => $ds['d_key']));
    $adtlFontClass = $icon == $deviceIcons['default'] || $ds['d_room'] == 'unknown' || $ds['d_type'] == 'MAINTENANCE' ? 'faint' : '';
    if($ds['d_added'] > time()-60*30) $adtlFontClass = 'bright';
    
    ?><tr class="<?=  $adtlFontClass ?>row">
      <td><?= $info ?></td>
      <td style="text-align:center;"><a href="<?= $editUrl ?>" title="<?= $ds['d_type'] ?>" class="fa fa-lg fa-<?= $icon ?> <?= $adtlFontClass ?>"></a></td>
      <td><a href="<?= $editUrl ?>" class="<?= $adtlFontClass ?>">#<?= $ds['d_key'] ?></a></td>
      <td style="text-align:center;"><?= getCellCheckbox($ds, 'd_visible') ?></td>
      <td class="<?= $adtlFontClass ?>"><?= $ds['d_bus'].':'.$ds['d_id'] ?></td>
      <td class="<?= $adtlFontClass ?>"><?= getCellEditor($ds, 'd_name') ?></td>
      <td class="<?= $adtlFontClass ?>"><?= getCellEditor($ds, 'd_alias') ?></td>
      <td class="<?= $adtlFontClass ?>"><?= getCellEditor($ds, 'd_room') ?></td>
      <td><?
      
      if($ds['d_bus'] == 'HM')
        print('<a class="small" href="'.actionUrl('params', 'devices', array('key' => $ds['d_key'])).'">HM</a>');
      
      ?></td>
    </tr><? 
  }

?></table>
<script>

editLock = {};

function saveCheckbox(spanId) {
  var cb = $('#'+spanId);
  $.post('?/devices/ajax_savefield', { f : cb.data('field'), key : cb.data('key'), v : cb.prop('checked') ? 'Y' : 'N' });
}

function saveCell(spanId) {
  var cb = $('#'+spanId);
  var content = $('#EDT_'+spanId).val();
  $.post('?/devices/ajax_savefield', { f : cb.data('field'), key : cb.data('key'), v : content });
  $('#'+spanId).text(content);
  delete editLock[spanId];
}

function editCell(spanId) {

  if(editLock[spanId]) return;
  
  editLock[spanId] = true;
  var content = $('#'+spanId).text();
  $('#'+spanId).html('<input type="text" id="EDT_'+spanId+'" onkeypress="if(event.keyCode==13)saveCell(\''+spanId+'\');" onblur="saveCell(\''+spanId+'\');"/>');
  $('#EDT_'+spanId).val(content);
  document.getElementById('EDT_'+spanId).focus();

}  

</script>