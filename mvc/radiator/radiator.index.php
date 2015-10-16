<?
  
  $this->getSettings($_REQUEST['id']);
    
?>

<h2>Available Radiators</h2>

<ul>
  <?
  foreach($this->radiatorSettings as $rkey => $rs)
  {
    ?><li><a 
        style="font-weight: bold"
        href="<?= actionUrl('minion', 'radiator', array('id' => $rkey)) ?>">
      <?= htmlspecialchars(@first($rkey, $rs['title'])) ?>
    </a><br/>
      <span style="opacity: 0.75"><?= htmlspecialchars(@first($rs['url'], $rs['image'])) ?></span>
      <br/><br/>
    </li><?
  }    
  ?>
</ul>