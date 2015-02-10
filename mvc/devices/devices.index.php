<div id="wstate"></div>
<?

include('templates/modeset.php');

?>
<div id="container" style="margin-left: 60px;"><?
  
include('templates/check-timer-system.php');

$renderer = new H2DeviceRenderer();

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