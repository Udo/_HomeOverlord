<?

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
      onclick=""
      class="bigBtn">
      <a href="?controller=events&action=manual&id=<?= urlencode($tr) ?>"><?= $tr ?></a>
    </div><?
  }

?>
</div>
<?  

?><style>
.bigBtn {
  display:inline-block; width: 160px; height: 80px; overflow: hidden;margin:12px;padding: 12px;border-radius:8px;border:1px solid gray;text-align:center;
}
</style>
