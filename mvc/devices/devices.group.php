<?= $this->_getSubmenu2('groups') ?>
<h1 style="font-size: 120%;"><?= htmlspecialchars($_REQUEST['id'])?></h1>
<?

$group = H2NVStore::get('group/'.$_REQUEST['id']);

if($_POST['cmd'] == 'change')
{
  if($_REQUEST['inGroup'] == 'Y')
    $group[] = $_REQUEST['device'];
  else
  {
    $idx = array_search($_REQUEST['device'], $group);
    if($idx !== false)
      unset($group[$idx]);
  }
  H2NVStore::set('group/'.$_REQUEST['id'], $group);
  die();
}

$evt = new H2Event();

$prevRoom = '';
foreach(db()->get('SELECT * FROM devices WHERE d_visible = "Y" AND d_room != "unknown" ORDER BY d_room,d_name') as $ds)
{
  if($prevRoom != $ds['d_room'])
  {
    print('<h2 style="margin:0;padding-top: 8px;">'.htmlspecialchars($ds['d_room']).'</h2>');
    $prevRoom = $ds['d_room'];
  }
  ?><div>
    <input type="checkbox" id="c_<?= $ds['d_key'] ?>" 
      <?= array_search($ds['d_key'], $group) ? 'checked' : '' ?>
      onchange="changeGroup(<?= $ds['d_key'] ?>, $(this).is(':checked'))"/>
    <label for="c_<?= $ds['d_key'] ?>"><?= htmlspecialchars($ds['d_name'].' ('.first($ds['d_alias'], $ds['d_key'])) ?>)</label>
  </div><?

}

?>

<script>

function changeGroup(deviceKey, val)
{
  $.post('<?= actionUrl('group', 'devices') ?>',
    { id : '<?= htmlspecialchars($_REQUEST['id']) ?>', device : deviceKey, 'inGroup' : (val ? 'Y' : 'N'), cmd : 'change' },
    function(data) {
    });
}

</script>

