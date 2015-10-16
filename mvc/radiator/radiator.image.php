<?php
  
  $GLOBALS['config']['page']['template'] = 'blank';
  $cfg = $this->getSettings($_REQUEST['id']);
  
?>

<img src="<?= $cfg['image'] ?>"/>

<style>
  
  body {
    overflow: hidden;
    background-color: #000;
  }
  
  img {
    width: 100%;
  }
  
</style>