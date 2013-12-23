<?= $this->_getSubmenu2() ?>
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

?>
<table class="devicetable">
<thead>
  <tr>
    <th>#</th>
    <th>Address</th>
    <th>Type</th>
    <th>Visible</th>
    <th>Alias</th>
    <th>Room</th>
    <th>Name</th>
  </tr>
</thead><?

  foreach(o(db)->get('SELECT * FROM devices ORDER BY d_room,d_name') as $ds) 
  {
    ?><tr>
      <td>#<?= $ds['d_key'] ?></td>
      <td><?= $ds['d_bus'].':'.$ds['d_id'] ?></td>
      <td><?= $ds['d_type'] ?></td>
      <td><?= getCellCheckbox($ds, 'd_visible') ?></td>
      <td><?= getCellEditor($ds, 'd_alias') ?></td>
      <td><?= getCellEditor($ds, 'd_room') ?></td>
      <td><?= getCellEditor($ds, 'd_name') ?></td>
      <td><?
      
      if($ds['d_bus'] == 'HM')
        print('<a href="'.actionUrl('params', 'devices', array('key' => $ds['d_key'])).'">Parameters</a>');
      
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