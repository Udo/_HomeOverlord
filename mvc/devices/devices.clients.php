<?= $this->_getSubmenu2() ?>

<table><tr>
  <th>Client</th>
  <th>Last Checkin</th>
</tr><?

$clientList = db()->get('SELECT * FROM nvstore ORDER BY nv_lastupdate DESC LIMIT 20');
foreach($clientList as $client)
{
  $cd = json_decode($client['nv_data'], true);
  ?><tr>
    <td><?= htmlspecialchars($cd['name']) ?></td>
    <td><a href="<?= actionUrl('client_settings', 'devices', array('id' => $client['nv_key'])) ?>"><?= $client['nv_key'] ?></a></td>
    <td><?= date('Y-m-d H:i', $client['nv_lastupdate']) ?></td>
  </tr><?
}

?></table>