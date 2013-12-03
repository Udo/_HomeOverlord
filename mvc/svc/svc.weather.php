<pre><?

$owFileName = 'data/openweather.json';
$weatherReportFile = 'data/weather.json';

if(time() - filemtime($owFileName) > 60*2)
{
  print('last called: '.(time() - filemtime($owFileName)).' seconds ago'.chr(10));

  unlink('data/openweather.json');
  unlink('data/weather.json');

  $weatherReq = cqrequest(array(array('url' => 'http://api.openweathermap.org/data/2.5/weather?q='.urlencode(cfg('geo/city')).'&mode=json&units=metric')));
  $owd = $weatherReq['data'];
  file_put_contents($owFileName, json_encode($owd));
  
  foreach($owd['weather'] as $w)
  {
    $data['icon'] = 'http://openweathermap.org/img/w/'.$w['icon'].'.png';
    $desc[] = $w['description'];
  }
  
  $data['description'] = implode(', ', $desc);
  
  $data['tecur'] = number_format($owd['main']['temp'], 1);
  $data['wscur'] = number_format($owd['wind']['speed'], 1);
  
  print_r($data);
  
  if(sizeof($data) > 0)
    file_put_contents($weatherReportFile, json_encode($data));
}
else
{
  print('from cache: '.chr(10));
  print_r(json_decode(file_get_contents($weatherReportFile), true));
}

?></pre>