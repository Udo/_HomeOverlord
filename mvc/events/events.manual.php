<?= H2Configuration::getUserMenu() ?><?

  $triggerList = array();

  foreach(o(db)->get('SELECT * FROM events') as $evt)
  {
    $triggerList[$evt['e_address']] = $evt['e_address'];
    $triggerList[$evt['e_address_rev']] = $evt['e_address_rev'];
  }
  
?>
<div class="iconOpt">
<?

  if(isset($_REQUEST['id']))
  {
    
  }

  asort($triggerList);

  foreach($triggerList as $tr) if($tr != '')
  {
    ?><div 
      onclick="eventCommand('<?= htmlspecialchars($tr) ?>')"
      class="bigBtn"><br/>
      <?= str_replace('-', '-<br/>', $tr) ?>
    </div><?
  }

?>
</div>
<?  

?>

<script>

function eventCommand(eventName)
{
  $.post('<?= actionUrl('ajax_trigger', 'svc') ?>', 
    { event : eventName },
    function(data) { console.log(data); });
}

</script>