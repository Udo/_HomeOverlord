<ul><?

$GLOBALS['pagetitle'] = 'Device Groups';

$grps = o(db)->get('SELECT * FROM groups ORDER BY g_name');

foreach($grps as $g)
{
  ?><li><a href="?controller=devices&action=group&id=<?= $g['g_key'] ?>">
    <?= htmlspecialchars($g['g_name']) ?> #<?= $g['g_key'] ?>
    </a> &nbsp; &middot; 
    <a onclick="if(confirm('Are you sure you want to delete this group?')) document.location.href='<?= actionUrl('group_delete', 'devices', array('id' => $g['g_key'])) ?>';">delete</a>
  </li><?
}

?></ul>

<hr/>

<form action="?" method="post">
  <input type="hidden" name="controller" value="devices"/>
  <input type="hidden" name="action" value="group_new"/>
  <input type="text" name="name" value="" placeholder="new group name"/>
  <input type="submit"/>
</form>