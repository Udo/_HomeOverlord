<pre><?


$owFileName = 'data/openweather.json';
$weatherReportFile = 'data/weather.json';

if(time() - filemtime($owFileName) > 60*2)
{
  print('last called: '.(time() - filemtime($owFileName)).' seconds ago'.chr(10));

  $weatherReq = cqrequest(array(array('url' => 'http://api.openweathermap.org/data/2.5/weather?q='.urlencode(cfg('geo/city')).'&mode=json&units=metric')));
  
  $owd = $weatherReq['data'];
  
  if(sizeof($owd) > 0)
  {
    unlink('data/openweather.json');
    unlink('data/weather.json');
  
    file_put_contents($owFileName, json_encode($owd));
    
    foreach($owd['weather'] as $w)
    {
      $wif = 'w/'.$w['icon'].'.png';
      $data['icon'] = 'http://openweathermap.org/img/'.$wif;
      if(!file_exists('icons/'.$wif))
      {
        mkdir('icons/w/');
        shell_exec('curl "'.$data['icon'].'" > icons/'.$wif);
      }
      $desc[] = $w['description'];
    }
    
    $data['description'] = implode(', ', $desc);
    
    $data['tecur'] = number_format($owd['main']['temp'], 1);
    $data['wscur'] = number_format($owd['wind']['speed'], 1);
    
    include('lib/moon.php');
    $moon = new Solaris\MoonPhase();
    $data['moonlight'] = number_format($moon->illumination(), 1)*100;
    $data['moonstage'] = $moon->phase() < 0.5 ? 'waxing' : 'waning';
  
    print_r($data);
    
    if(sizeof($data) > 0)
      file_put_contents($weatherReportFile, json_encode($data));
  }
}
else
{
  print('from cache: '.chr(10));
  print_r(json_decode(file_get_contents($weatherReportFile), true));
}

?></pre>