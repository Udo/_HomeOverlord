<?
  
  $this->getSettings();
  
  if($_POST['action'] == 'save')
  {
    $this->radiatorSettings = $this->textToConfig($_POST['config']);
    $this->saveSettings();
    $this->broadCastUpdate();
  }
  
?>

<form action="" method="post">
  
  <input type="hidden" name="action" value="save"/>
  
  <textarea 
    style="width:99%;height:50vh;"
    name="config"><?= $this->configToText() ?></textarea>
  
  <input type="submit" value="Save"/>
  
</form>
