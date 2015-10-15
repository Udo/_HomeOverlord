<?
  
  $cfg = $this->getSettings($_REQUEST['id']);
  
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
    height: calc(100vh - 32px);
    border:none;"
  src="<?= htmlspecialchars($cfg['url']) ?>"></iframe>
  
<script>

var radiatorSettings = <?= json_encode($cfg) ?>;
var radiatorId = <?= json_encode($this->radiatorId) ?>;

messageHandlers.radiator = function(msg) {
  console.log(msg);
  if(msg.id == radiatorId) {
    $('#minion-screen').attr('src', msg.url);
  }
}

</script>

<?
  
  //broadcast(array('type' => 'radiator', 'test' => time()));