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
    
    if($sunRiseIn > -600 && $sunRiseIn <= 0)
      $eventAdresses[] = 'SUNRISE'.$sunRiseIn;
    if($sunRiseIn < 600 && $sunRiseIn >= 0)
      $eventAdresses[] = 'SUNRISE+'.$sunRiseIn;
    if($sunSetIn > -600 && $sunSetIn <= 0)
      $eventAdresses[] = 'SUNSET'.$sunSetIn;
    if($sunSetIn < 600 && $sunSetIn >= 0)
      $eventAdresses[] = 'SUNSET+'.$sunSetIn;
    if($sunRiseIn == 0)
      $eventAdresses[] = 'SUNRISE';
    if($sunSetIn == 0)
      $eventAdresses[] = 'SUNSET';
  }
  
  $isDay = $t >= strtotime(date('H:i', getSunrise(false))) && $t <= strtotime(date('H:i', getSunset(false)));
  $dayString = $isDay ? 'DAY' : 'NIGHT';
    
  if(
    ($mainWeather['id'] >= 200 && $mainWeather['id'] < 250) || 
    ($mainWeather['id'] >= 803 && $mainWeather['id'] < 850) || 
    ($mainWeather['id'] >= 501 && $mainWeather['id'] < 550))
    $eventAdresses[] = $dayString.'-DARK';

  $eventAdresses[] = $dayString.'-'.strtoupper($mainWeather['main']);
  $eventAdresses[] = 'TICK-'.strtoupper($mainWeather['main']);
  
  // --------
  foreach(array('', '_rev') as $addressType)
  {
    $GLOBALS['reverse_action'] = $addressType == '_rev';
    $where = array();
    foreach($eventAdresses as $e)
      $where[] = '(e_address'.$addressType.' = ?)';
      
    $qry = 'SELECT * FROM events
      WHERE e_type="T" AND e_lastcalled < '.$coolDown.' AND ('.implode(' OR ', $where).')';
    $triggers = o(db)->get($qry, $eventAdresses);

    $report = array();
    foreach($triggers as $eds)
    {
      $this->ignoreExecution = false;
      $result = $this->execTriggerScript($eds['e_code'], array(
        'ds' => &$eds,
        'event' => $eds['e_address'.$addressType],
        ));
      $rline = 'triggered #'.$eds['e_key'].' '.$eds['e_address'.$addressType].' '.$result;
      $report[] = $rline;
      WriteToFile('log/tick.events.log', date('Y-m-d H:i:s').' '.$rline.chr(10));
      if(!$this->ignoreExecution)
        o(db)->query('UPDATE events SET e_lastcalled = '.time().' WHERE e_key = '.$eds['e_key']);
    }    
  }
      
  print(json_encode(array(
    'register' => $eventAdresses, 
    'report' => $report, 
    'profile' => $GLOBALS['profiler_log'])));
    
}


