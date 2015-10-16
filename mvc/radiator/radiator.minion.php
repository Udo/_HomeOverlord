<?
  
  $cfg = $this->getSettings($_REQUEST['id']);
  unset($cfg['reload']);
  $cfg['id'] = $_REQUEST['id'];
  
?>

<iframe 
  id="minion-screen"
  style="
    position: absolute;
    left: 0; right: 0;
    top: 32px;
    bottom: 0;
    width: 100%;
    height: 100vh;
    height: calc(100vh - 35px);
    border:none;"
    src="?/radiator/empty&text=<?= urlencode($_REQUEST['id']) ?>"></iframe>
  
<script>

var radiatorSettings = <?= json_encode($cfg) ?>;
var radiatorId = <?= json_encode($this->radiatorId) ?>;

messageHandlers.radiator = function(msg) {
  if(msg.id == radiatorId || msg.id == <?= json_encode($_REQUEST['id']) ?>) {
    if(msg.reload == 'yes')
      document.location.reload();
    else if(msg.image)
      $('#minion-screen').attr('src', '?/radiator/image&id=<?= urlencode($_REQUEST['id']) ?>');
    else if(msg.url)
      $('#minion-screen').attr('src', msg.url);
  }
}

$('#lefthdr').text(<?= json_encode(@first($cfg['title'], $_REQUEST['id'])) ?>);

messageHandlers.radiator(radiatorSettings);

</script>

<style>
  
  body, iframe {
    overflow: hidden;
  }
  
</style>