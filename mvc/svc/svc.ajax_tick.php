<?php

$mode = new H2Mode();
if($mode->current != 'No Auto')
{
    
  $weather = json_decode(file_get_contents('data/openweather.json'), true);
  @$mainWeather = $weather['weather'][0];
  
  $weekDay = date('N');
  $WE = $weekDay == 6 || $weekDay == 7 ? 'WEEKEND' : 'WEEKDAY';
  $weekDayName = $GLOBALS['weekdays'][$weekDay];
  $coolDown = time()-120;
  $eventAdresses = array('TICK');
  
  foreach(array(time(), time()-60) as $t)
  {
     foreach(array(
      'TIME-'.date('H:i', $t),
      $weekDayName.'-'.date('H:i', $t),
      $WE.'-'.date('H:i', $t),
      'MINUTE-'.date('i', $t),
      ) as $e) 
      $eventAdresses[] = $e;
      
    $sunRiseIn = round(($t - strtotime(date('H:i', getSunrise(false))))/60);
    $sunSetIn = round(($t - strtotime(date('H:i', getSunset(false))))/60);
    
    $sunEventDistance = 300; #minutes
    
    if($sunRiseIn > -$sunEventDistance && $sunRiseIn <= 0)
      $eventAdresses[] = 'SUNRISE'.$sunRiseIn;
    if($sunRiseIn < $sunEventDistance && $sunRiseIn >= 0)
      $eventAdresses[] = 'SUNRISE+'.$sunRiseIn;
    if($sunSetIn > -$sunEventDistance && $sunSetIn <= 0)
      $eventAdresses[] = 'SUNSET'.$sunSetIn;
    if($sunSetIn < $sunEventDistance && $sunSetIn >= 0)
      $eventAdresses[] = 'SUNSET+'.$sunSetIn;
    if($sunRiseIn == 0)
      $eventAdresses[] = 'SUNRISE';
    if($sunSetIn == 0)
      $eventAdresses[] = 'SUNSET';
  }
  
  $eventAdresses[] = 'TICK-'.strtoupper($mainWeather['main']);
  
  $this->callEventHandlers(
    $eventAdresses, 
    $_REQUEST);
    
  print(json_encode($GLOBALS['log']));
    
  profile_point('done');
  $_REQUEST['log'] = $GLOBALS['log'];
  WriteToFile('log/event.log', date('Y-m-d H:i:s').' tick event '.json_encode($_REQUEST).chr(10)
    .'- '.json_encode($eventAdresses).chr(10)
    .'- '.json_encode($GLOBALS['log']).chr(10)   
    #.'- '.json_encode($GLOBALS['profiler_log']).chr(10)
    );
      
}


