<div id="wstate"></div>
<div id="container"><?

$renderer = new H2DeviceRenderer();
$nv = new H2NVStore();

$clientIdentifier = 'client/'.$_SERVER['REMOTE_ADDR'];
$clientSettings = $nv->get($clientIdentifier);
$clientSettings['lastseen'] = time();
$nv->set($clientIdentifier, $clientSettings);

foreach($this->devices as $dtype => $dt) if(!$clientSettings['hide'.$dtype])
{  
  ?><div class="smalltext bottomborder"><?= htmlspecialchars($dtype) ?></div><?
  foreach($dt as $ds)
  {
    $renderer->display($ds);
  }
}
?></div>

<? include('templates/weatherinfo.php'); ?>