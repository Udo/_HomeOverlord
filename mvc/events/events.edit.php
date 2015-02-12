<?= H2Configuration::getAdminMenu() ?>
<?

$ds = db()->getDS('events', $_REQUEST['id']);

if(isset($_POST['id']))
{
  foreach(array('e_address', 'e_address_rev', 'e_code') as $f)
    $ds[$f] = $_REQUEST[$f];
  $ds['e_key'] = db()->commit('events', $ds);
}

?>

<form action="<?= actionUrl('edit', 'events') ?>" method="POST">
<table class="settingsTable">
  
  <input type="hidden" name="id" value="<?= first($ds['e_key'], '0') ?>"/>
  <tr>
    <td>Action</td>
    <td><input type="text" name="e_address" value="<?= htmlspecialchars(@first($ds['e_address'], $_REQUEST['eventaddress']))?>"/></td>
  </tr>
  <tr>
    <td>Reverse</td>
    <td><input type="text" name="e_address_rev" value="<?= htmlspecialchars($ds['e_address_rev'])?>"/></td>
  </tr>
  <tr>
    <td valign="top">Code</td>
    <td><textarea name="e_code"><?= htmlspecialchars($ds['e_code'])?></textarea>
  </tr>
  <tr>
    <td></td>
    <td><input type="submit" value="Save"/></td>
  </tr>


</table>
</form>

