<?
$panelSize = first($_REQUEST['zoom'], 250);
?>
<div id="camsPanel"><?

foreach(cfg('cameras/cams') as $cam)
{
  ?><div>
    <div style="text-align:center;padding:4px;"><?= htmlspecialchars(first($cam['title'], $cam['id']))?></div>
    <a href="<?= actionUrl('single', 'cam', array('id' => $cam['id'])) ?>">
      <img src="data/cam/<?= $cam['id'] ?>_mid.jpg" width="100%"/>
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
    }, 1000*70);
    
</script>

<style>
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