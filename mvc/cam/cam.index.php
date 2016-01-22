<?
$panelSize = first($_REQUEST['zoom'], 250);
?>
<div class="iconOpt"><?

  
  foreach(array(
    array('url' => actionUrl('refresh', 'cam'), 'icon' => 'refresh', 'title' => 'Refresh'),
    ) as $itm)
  {
    ?><div onclick="document.location.href='<?= $itm['url'] ?>';">
      <i class="asCharacter fa fa-<?= $itm['icon'] ?> fa-3x" style="margin-top: 12px;margin-bottom: 6px;"></i><br/>
      <?= $itm['title'] ?>
    </div><?
  }

?></div>

<div id="camsPanel"><?

foreach(cfg('cameras/cams') as $cam)
{
  $pic = 'data/cam/'.$cam['id'].'_mid.jpg';
  ?><div>
    <div style="text-align:center;padding:4px;"><?= htmlspecialchars(first($cam['title'], $cam['id']))?></div>
    <a href="<?= actionUrl('single', 'cam', array('id' => $cam['id'])) ?>">
      <div class="imgContainer">
      <?
      if(file_exists($pic) && filesize($pic) > 0)
      {
      ?><img src="<?= $pic ?>" width="100%"/><?
      }
      else
      {
      ?><div style="padding:32px">( no data )</div><?
      } 
      ?>
      </div>
    </a>
  </div><?
}

?></div>

<script>
  
  messageHandlers.camtick = function() {
    window.location.reload(true);
  };

  setTimeout(function() {
    window.location.reload(true);
    }, 1000*20);
    
</script>

<style>
.imgContainer {
  display: inline-block;
  width:100%;
  height:300px;
  background:rgba(128,128,128,0.3); 
  text-align: center;  
  overflow: hidden;
}
#camsPanel > div {
  display: inline-block;
  width: <?= $panelSize ?>px;
  height: <?= round(3*$panelSize/4)+30 ?>px;
  margin-right: 16px;
  margin-bottom: 16px;
  border: 1px solid rgba(0,0,0,0.3);
  box-shadow: 0px 0px 12px rgba(0,0,0,0.25);
  overflow: hidden;
  vertical-align: middle;
}
#camPanel {
  vertical-align: middle;
}
</style>