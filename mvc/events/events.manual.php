<?= $this->_getSubmenu2(); ?><?

  $triggerList = array();

  foreach(o(db)->get('SELECT * FROM events') as $evt)
  {
    $triggerList[$evt['e_address']] = $evt['e_address'];
    $triggerList[$evt['e_address_rev']] = $evt['e_address_rev'];
  }
  
?>
<div>
<?

  if(isset($_REQUEST['id']))
  {
    
  }

  asort($triggerList);

  foreach($triggerList as $tr) if($tr != '')
  {
    ?><div 
      onclick="eventCommand('<?= htmlspecialchars($tr) ?>')"
      class="bigBtn">
      <?= $tr ?>
    </div><?
  }

?>
</div>
<?  

?><style>
.bigBtn {
  display:inline-block; width: 160px; height: 44px; overflow: hidden;margin:12px;padding: 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.2);text-align:center;
}
.bigBtn:hover {
  background: rgba(255,255,255,0.1);
  cursor:pointer;
}
</style>

<script>

function eventCommand(eventName)
{
  $.post('<?= actionUrl('ajax_trigger', 'svc') ?>', 
    { event : eventName },
    function(data) { console.log(data); });
}

</script>