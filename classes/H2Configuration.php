<?php

class H2Configuration
{

  function __construct()
  {
    error_reporting(E_ALL ^ E_NOTICE);
    $this
      ->checkPHP()
      ->setCustomErrorHandler()
      ->load();
  }
  
  function checkPHP()
  {
    # mandate at least PHP 5.3
    $version = explode('.', phpversion());
    if(!($version[0] >= 5 && $version[1] >= 3)) die('Error - PHP 5.3 or greater needed'); 
    
    # mandatory extensions
    $missingExtensions = array();
    foreach(array('curl', 'json', 'mysql', 'gd') as $extensionName)
      if(!extension_loaded($extensionName)) $missingExtensions[] = $extensionName;
    if($missingExtensions)
      die('Error - the following PHP extensions are required but missing: '.implode(', ', $missingExtensions));
      
    return($this);
  }
  
  function setCustomErrorHandler()
  {
    set_error_handler(function($n, $s, $f, $l, $c) {
      if($n < 8) // skip notices
        WriteToFile('log/error.log', $s.' in '.$f.' line '.$l."\n");
      });
    return($this);
  }
  
  function load()
  {
    require($GLOBALS['APP.BASEDIR'].'/config/defaults.php');
    @include($GLOBALS['APP.BASEDIR'].'/config/settings.php');
    return($this);
  }
  
  function save()
  {
  
  }

}

