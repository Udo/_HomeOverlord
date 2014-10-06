<?php

  $isDay = $t >= strtotime(date('H:i', getSunrise(false))) && $t <= strtotime(date('H:i', getSunset(false)));
  $dayString = $isDay ? 'DAY' : 'NIGHT';

  $this->callEventHandlers(
    array($_POST['event']), 
    $_POST);
    
  print(json_encode($GLOBALS['log']));
    
  $_POST['log'] = $GLOBALS['log'];
  WriteToFile('log/event.log', date('Y-m-d H:i:s').' trigger '.json_encode($_POST).chr(10));
