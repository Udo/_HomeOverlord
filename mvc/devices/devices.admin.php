<?= $this->_getSubmenu2() ?>

<?

  $adminModules = array(
    array('title' => 'Clients', 'icon' => '', 'url' => actionUrl('clients', 'devices')),
    array('title' => 'Device List', 'icon' => '', 'url' => actionUrl('show', 'devices')),
    array('title' => 'HM Command Line', 'icon' => '', 'url' => actionUrl('cli', 'devices')),
    array('title' => 'HM Pair Device', 'icon' => '', 'url' => actionUrl('pairhm', 'devices')),
    array('title' => 'Groups', 'icon' => '', 'url' => actionUrl('groups', 'devices')),
    array('title' => 'Modes', 'icon' => '', 'url' => actionUrl('modes', 'devices')),
    );

?>

<ul>
<?
foreach($adminModules as $am) {
?>
  <ol><a href="<?= $am['url'] ?>"><?= htmlspecialchars($am['title']) ?></a></ol>
<?
}
?>
</ul>