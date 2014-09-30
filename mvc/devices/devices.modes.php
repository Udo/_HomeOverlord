<?= $this->_getSubmenu2() ?>

<?

if($_POST['modes'])
{
  $modes = array();
  foreach(explode(chr(10), $_POST['modes']) as $m)
  {
    if(trim($m) != '')
      $modes[] = trim($m);
  }
  $nv->set('pref/modes', $modes);
}
else
{
  $modes = $nv->get('pref/modes');
}

$modeLines = implode(chr(10), $modes);
?>

<form action="<?= actionUrl('modes', 'devices') ?>" method="post">

  Modes:<br/>
  <textarea name="modes" style="width: 90%;height:200px;"><?= $modeLines ?></textarea><br/>
  
  <input type="submit" value="Save"/>

</form>
<?



?>