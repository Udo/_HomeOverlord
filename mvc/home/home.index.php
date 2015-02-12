<div class="bottomborder">
  Main Functions
</div>

<div class="iconOpt"><?

  
  foreach(H2Configuration::getMenuLinks() as $itm)
  {
    ?><div onclick="document.location.href='<?= $itm['url'] ?>';">
      <i class="asCharacter fa fa-<?= $itm['icon'] ?> fa-3x" style="margin-top: 12px;margin-bottom: 6px;"></i><br/>
      <?= $itm['title'] ?>
    </div><?
  }

?></div>

<div class="bottomborder">
  Configuration
</div>

<div class="iconOpt"><?

  foreach(H2Configuration::getAdminLinks() as $itm)
  {
    ?><div onclick="document.location.href='<?= $itm['url'] ?>';">
      <i class="asCharacter fa fa-<?= $itm['icon'] ?> fa-3x" style="margin-top: 12px;margin-bottom: 6px;"></i><br/>
      <?= $itm['title'] ?>
    </div><?
  }

?></div>
