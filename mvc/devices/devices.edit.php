<?= $this->_getSubmenu2('show') ?>

<form action="?" method="post">
<input type="hidden" name="controller" value="<?= $_REQUEST['controller'] ?>"/>
<input type="hidden" name="action" value="<?= $_REQUEST['action'] ?>"/>
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<?
$doSave = isset($_POST['controller']);

$ds = getDeviceDS($_REQUEST['key']);
$dev = HMRPC('getDeviceDescription', array($ds['d_id']));
$_REQUEST['actionEvents'] = array();

?>
<form action="<?= actionUrl('edit', 'devices') ?>" method="post">
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<table style="margin-top: -8px; margin-bottom: 12px; max-width: 800px; width: 100%;">
  <tr>
    <td style="text-align:right">
      <span class="faint">Device</span>   
    </td>
    <td width="*">
      <input type="text" style="width: 100%" name="d_name" value="<?= htmlspecialchars(first($ds['d_name'])) ?>"/>
    </td>
  </tr>
  <tr>
    <td style="text-align:right">
      <span class="faint">Type</span>   
    </td>
    <td width="*">
      <?= first($dev['TYPE'], $ds['d_type']) ?>
      <span class="faint">ID</span> <?= $ds['d_id'] ?> 
      <span class="faint">Alias</span> <?= first($ds['d_alias'], '#'.$ds['d_key']) ?>  
      <span class="faint">Name</span> <?= first($ds['d_name']) ?></div>
    </td>  
  </tr><?
  $related = array();
  $idnr = $ds['d_id'];
  $idroot = CutSegment(':', $idnr);
  if($ds['d_bus'] == 'HM')
    $related[] = '<a href="'.actionUrl('params', 'devices', array('key' => $ds['d_key'])).'">HomeMatic</a>';
  foreach(o(db)->get('SELECT d_key,d_id,d_alias,d_type FROM devices WHERE d_id LIKE "'.$idroot.'%" ORDER BY d_id') as $dds)
  {     
    $related[] = '<a href="'.actionUrl('edit', 'devices', array('key' => $dds['d_key'])).'" style="'.($dds['d_key'] == $ds['d_key'] ? 'font-weight:bold;' : '').'">'.
      htmlspecialchars(first($dds['d_alias'], $dds['d_type'])).' '.$dds['d_id'].'</a>';//array($ds['d_id'] => $ds['d_id'].' ('.first($ds['d_alias'], $ds['d_id']).')');
  }

  print('<tr><td valign="top" style="text-align: right;"><span class="faint">Compound</span></td><td>'.implode(', ', $related).'</td></tr>');

  $editable = array('d_bus' => 'Bus System', 'd_id' => 'Identifier', 'd_type' => 'Type', 'd_room' => 'Room', 'd_name' => 'Name', 'd_alias' => 'Alias');
  
  foreach($editable as $fn => $fncap)
  {
    if($_POST['key'])
      $ds[$fn] = $_POST[$fn];
    ?><tr>
      <td style="text-align:right">
         <?= $fncap ?>
      </td>
      <td width="*">
        <input type="text" style="width: 100%" name="<?= $fn ?>" value="<?= htmlspecialchars(first($_POST[$fn], $ds[$fn])) ?>"/>
      </td>
    </tr><?
  }

?>

<tr>
    <td style="text-align:right">
       
    </td>
    <td width="*">
      <?
      if($_POST['key'])
      {
        o(db)->commit('devices', $ds);
        ?><div class="banner">Your changes have been saved.</div><?
      }
      
      ?><hr/>
      <input type="submit" value="Save Changes"/>
    </td>
  </tr>


</table></form>