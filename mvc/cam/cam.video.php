<?
$panelSize = first($_REQUEST['zoom'], 640);
?>
<br/>
<div id="camsPanel"><?

$camTitle = '';
$thisCam = array();
foreach(cfg('cameras/cams') as $cam) if($cam['id'] == $_REQUEST['id'])
{
  $thisCam = $cam;
  $camTitle = htmlspecialchars(first($cam['title'], $cam['id']));
  ?><a href="<?= actionUrl('index', 'cam') ?>">
    <iframe frameborder="0" src="<?= $cam['videoUrl'] ?>" width="80%" height="600"></iframe></a><?
}

?></div>

<div style="text-align: center;">
  <?
  if($thisCam['photoUrl'])
  {
    ?><a href="<?= actionUrl('single', 'cam', array('id' => $thisCam['id'])) ?>">&gt; Still Image</a><?
  }
  ?>
</div>

<script>
  
  $('#lefthdr').text('<?= $camTitle ?>');
  
</script>

<style>
#camsPanel {
  margin-top: -22px;
  margin-left: -10px;
  text-align: center;
}

</style>