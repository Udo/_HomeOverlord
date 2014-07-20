<?php

  # init environment
  $GLOBALS['profiler_start'] = microtime();
  $GLOBALS['APP.BASEDIR'] = dirname(__FILE__);
  
  ob_start("ob_gzhandler");
  chdir($GLOBALS['APP.BASEDIR']);
  
  require('lib/h2genlib.php');
  require('lib/h2config.php'); 
  require('lib/h2ha.php'); 
  
  if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];

  o(new H2User(), 'user');

  profile_point('libraries loaded');
  
  o(new H2Dispatcher($_REQUEST))
    ->initEnvironment()
    ->receiveData()
    ->initController($_REQUEST['controller'])
    ->invokeAction($_REQUEST['action'])
    ->invokeView('main')
    ->invokeTemplate(cfg('page/template', 'page'))
    ->cleanup();
  	
?>