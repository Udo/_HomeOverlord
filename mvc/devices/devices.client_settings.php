<?= $this->_getSubmenu2() ?>

<h1>Settings for <?= htmlspecialchars($_REQUEST['id']) ?> <span id="saveStatus"></span></h1>

<?php
$nv = new H2NVStore();
$clientIdentifier = $_REQUEST['id'];
$clientSettings = $nv->get($clientIdentifier);

?>
<h2>Client Name 
<input type="text" value="<?= htmlspecialchars($clientSettings['name']) ?>"
  onchange="updateSetting('name', $(this).val());"/>
</h2>

<h2>Show Rooms</h2><?
foreach($this->devices as $dtype => $dt)  
{  
  ?><input type="checkbox" 
    onclick="updateSetting('hide<?= $dtype ?>', $(this).is(':checked') ? 0 : 1);"
    <?= !$clientSettings['hide'.$dtype] ? 'checked' : '' ?>> <?= htmlspecialchars($dtype) ?><br/><?  
}

?><script>

function updateSetting(sKey, sValue) {
  $.post('<?= actionUrl('ajax_client_update', 'devices') ?>', 
    { id : '<?= $_REQUEST['id'] ?>', key : sKey, value : sValue }, function(data) {
      $('#saveStatus').html('<span id="saveStatusElement">[saved]</span>');
      setTimeout(function() {
        $('#saveStatusElement').fadeOut('normal');
        }, 2000);
    });
}

</script>

