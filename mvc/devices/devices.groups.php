<?= $this->_getSubmenu2('groups') ?><ul><?

$GLOBALS['pagetitle'] = 'Device Groups';

if($_POST['name'])
  H2NVStore::set('group/'.$_POST['name'], array());
  
if($_REQUEST['remove'])
  db()->get('DELETE FROM nvstore WHERE nv_key = ?', array('group/'.$_REQUEST['remove']));

foreach(o(db)->get('SELECT * FROM nvstore 
  WHERE nv_key LIKE "group/%"
  ORDER BY nv_key') as $g)
{  
  $gname = substr($g['nv_key'], 6);
  
  ?><li><a href="<?= actionUrl('group', 'devices', array('id' => $gname)) ?>">
    <?= htmlspecialchars($gname) ?>
    </a> &nbsp; 
    | <a href="<?= actionUrl('groups', 'devices', array('remove' => $gname)) ?>">delete</a>
  </li><?
}

?></ul>

<hr/>

<form action="?" method="post">
  <input type="hidden" name="controller" value="devices"/>
  <input type="hidden" name="action" value="groups"/>
  <input type="text" name="name" value="" placeholder="new group name"/>
  <input type="submit"/>
</form>
