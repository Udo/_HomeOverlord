<?= $this->_getSubmenu2() ?>

<table><tr>
  <th>Name</th>
  <th>Client</th>
  <th>Last Checkin</th>
</tr><?

$clientList = db()->get('SELECT * FROM nvstore 
  WHERE nv_key LIKE "client/%"
  ORDER BY nv_lastupdate DESC LIMIT 20');
foreach($clientList as $client)
{
  $cd = json_decode($client['nv_data'], true);
  ?><tr>
    <td><?= htmlspecialchars($cd['name']) ?></td>
    <td><a 
      style="<?= 'client/'.$_SERVER['REMOTE_ADDR'] == $client['nv_key'] ? 'font-weight:bold' : '' ?>"
      href="<?= actionUrl('client_settings', 'devices', array('id' => $client['nv_key'])) ?>"><?= $client['nv_key'] ?></a></td>
    <td><?= ageToString($client['nv_lastupdate']) ?></td>
  </tr><?
}

?></table>

<hr/>
<input type="button" value="reload"
  onclick="document.location.href='<?= actionUrl('clients', 'devices', array('do' => 'reload')) ?>';"/> all screens
<?
if($_REQUEST['do'] == 'reload')
  broadcast(array('type' => 'reload'));
?>