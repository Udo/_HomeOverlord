<?
  
  $tzOffset = date('O')/100;

?>
<pre>
  Date: <?= date('Y-m-d H:i') ?> 
  UTC Offset: <?= $tzOffset ?> 
  Sunset: <?= date_sunset(time(), SUNFUNCS_RET_STRING, $GLOBALS['config']['geo']['lat'], $GLOBALS['config']['geo']['long'], $GLOBALS['config']['geo']['zenith'], $tzOffset) ?> 
  Sunrise: <?= date_sunrise(time(), SUNFUNCS_RET_STRING, $GLOBALS['config']['geo']['lat'], $GLOBALS['config']['geo']['long'], $GLOBALS['config']['geo']['zenith'], $tzOffset) ?> 

</pre>