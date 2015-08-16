<script>

weatherInfoState = 0;
weatherInfo = [];

leadingZero = function(v) {
  if(v < 10) v = '0'+v;
  return(v);
}

weatherAndTimeUpdate = function() {

  if(weatherInfo.length == 0) return;
  
  weatherInfoState++;
  if(weatherInfoState >= weatherInfo.length) weatherInfoState = 0;
  
  var d = new Date();
  $('#lefthdr').html(leadingZero(d.getHours())+':'+leadingZero(d.getMinutes())+
    '   <img src="'+document.weather.icon+'" height="28" align="absmiddle" style="margin-bottom: -8px;margin-top: -8px;"/> '+
    weatherInfo[weatherInfoState]
    );

}

document.weather = <?= file_get_contents('data/weather.json') ?>;
weatherAndTimeUpdate();

updateWeatherInfo = function() {
  $.get('data/weather.json?v='+Math.random(), {}, function(data) {
    
    document.weather = data;
    weatherInfo = [
      document.weather.description+' '+
      document.weather.tecur+'°C · '+Math.round(document.weather.wind.speed*36)/10+
      (document.weather.wind.gust ? ' - '+Math.round(document.weather.wind.gust*36)/10 : '')+
      ' km/h '+
      (document.weather.rain && document.weather.rain['1h'] ? ' · '+document.weather.rain['1h']+'mm/h' : ''),
      ];
      
    var dt = new Date();
    var currentMinutes = dt.getMinutes()+dt.getHours()*60;
    if(currentMinutes > sunSet - 120 && currentMinutes < sunSet + 120)
    {
      if(currentMinutes > sunSet)
        weatherInfo.push('sunset '+(currentMinutes-sunSet)+' min ago');
      else
        weatherInfo.push('sunset in '+(sunSet-currentMinutes)+'min');
    }
    else if(currentMinutes > sunRise - 120 && currentMinutes < sunRise + 120)
    {
      if(currentMinutes > sunRise)
        weatherInfo.push('sunrise '+(currentMinutes-sunRise)+' min ago');
      else
        weatherInfo.push('sunrise in '+(sunRise-currentMinutes)+'min');
    }

  if(currentSunState == 'night')
    weatherInfo.push('moon '+document.weather.moonlight+'% '+document.weather.moonstage);
    
  }, 'json');
  };

setInterval(weatherAndTimeUpdate, 3000);
  
setInterval(updateWeatherInfo, 60*1000);

updateWeatherInfo();

</script>