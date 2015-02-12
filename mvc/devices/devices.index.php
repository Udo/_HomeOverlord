<div id="wstate"></div>
<?

include('templates/modeset.php');
profile_point('modeset widget complete');

?>
<div id="container" style="margin-left: 60px;"><?
  
include('templates/check-timer-system.php');
profile_point('timer system check');

$renderer = new H2DeviceRenderer();
profile_point('H2DeviceRenderer ready');

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