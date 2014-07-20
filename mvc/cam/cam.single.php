<?
$panelSize = first($_REQUEST['zoom'], 640);
?>
<div id="camsPanel"><?

$camTitle = '';
foreach(cfg('cameras/cams') as $cam) if($cam['id'] == $_REQUEST['id'])
{
  $camTitle = htmlspecialchars(first($cam['title'], $cam['id']));
  ?><a href="<?= actionUrl('index', 'cam') ?>"><img src="data/cam/<?= $cam['id'] ?>_mid.jpg" width="80%"/></a><?
}

?></div>

<script>
  
  messageHandlers.camtick = function() {
    window.location.reload(true);
  };
  
  setTimeout(function() {
    window.location.reload(true);
    }, 1000*70);
  
  $('#lefthdr').text('<?= $camTitle ?>');
  
</script>

<style>
#camsPanel {
  margin-top: -22px;
  margin-left: -10px;
  text-align: center;
}

</style>