<div class="iconOpt"><?

  $items = array();
  
  $items[] = array('icon' => 'building-o', 'title' => l10n('devices.index'), 'url' => actionUrl('index', 'devices'));
  if(cfg('cameras'))
    $items[] = array('icon' => 'video-camera', 'title' => l10n('cam.index'), 'url' => actionUrl('index', 'cam'));
  $items[] = array('icon' => 'gear', 'title' => l10n('devices.admin'), 'url' => actionUrl('admin', 'devices'));
  
  foreach($items as $itm)
  {
    ?><div onclick="document.location.href='<?= $itm['url'] ?>';">
      <i class="asCharacter fa fa-<?= $itm['icon'] ?> fa-3x" style="margin-top: 12px;margin-bottom: 6px;"></i><br/>
      <?= $itm['title'] ?>
    </div><?
  }

?></div>

<hr/><br/>

<div class="iconOpt"><?

  $items = array();
  
  $items[] = array('icon' => 'flash', 'title' => l10n('events.index'), 'url' => actionUrl('index', 'events'));
  $items[] = array('icon' => 'tablet', 'title' => l10n('devices.clients'), 'url' => actionUrl('clients', 'devices'));
  $items[] = array('icon' => 'reorder', 'title' => l10n('devices.show'), 'url' => actionUrl('show', 'devices'));
  $items[] = array('icon' => 'refresh', 'title' => l10n('devices.pairhm'), 'url' => actionUrl('pairhm', 'devices'));
  
  foreach($items as $itm)
  {
    ?><div onclick="document.location.href='<?= $itm['url'] ?>';">
      <i class="asCharacter fa fa-<?= $itm['icon'] ?> fa-3x" style="margin-top: 12px;margin-bottom: 6px;"></i><br/>
      <?= $itm['title'] ?>
    </div><?
  }

?></div>
