<?
  
  if(filemtime('log/event.log') < time()-60*5)
  {
    ?><div class="banner">
      Warning: the timer system is not working.
    </div><?
    $nodeProc = explode(chr(10), shell_exec('ps aux | grep node'));
    $srvFound = false;
    foreach($nodeProc as $p)
      if(stripos($p, 'node srv.js') !== false) $srvFound = true;
    if(!$srvFound) 
    {
      ?><div class="banner">
        Warning: the Home Overlord service is not running.
      </div><?
    }
  }