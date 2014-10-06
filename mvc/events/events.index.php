<?= $this->_getSubmenu2(); ?>
<table class="devicetable"><?

foreach(o(db)->get('SELECT * FROM events ORDER BY e_address') as $evt)
{
  ?><tr onclick="document.location.href='<?= actionUrl('edit', 'events', array('id' => $evt['e_key'])) ?>'">
    <td><?= htmlspecialchars($evt['e_address']) ?></td>
    <td><?= htmlspecialchars($evt['e_address_rev']) ?></td>
    <td><?= htmlspecialchars(substr($evt['e_code'],0,60)) ?></td>
  </tr><?
}  

?></table>

<input type="button" value="New Event Handler"
  onclick="document.location.href='<?= actionUrl('edit', 'events') ?>';"/>

<style>

tr:hover {
  cursor: pointer;
}

</style>