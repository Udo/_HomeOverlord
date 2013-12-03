<?php
/*
 * Hubbub configuration library
 * Purpose: sets up and loads the configuration, also handles defaults
 */

/*
 * set up the auto class loader, this will automatically load any class source files
 */
function __autoload($class_name) 
{
  $classFile = $GLOBALS['APP.BASEDIR'].'/classes/'.basename($class_name).'.php';
  if(file_exists($classFile))
    include_once($classFile);
}

/* 
 * retrieve a config value (don't use $GLOBALS['config'] directly if possible) 
 */
function cfg($name, $default = null, $set = false)
{
	$vr = &$GLOBALS['config'];
	foreach(explode('/', $name) as $ni) 
	  if(is_array($vr)) $vr = &$vr[$ni]; else $vr = '';
	if($set)
	  $vr = $default; 
	return(first($vr, $default));
}

/*
 * some predefined constants, just for convenience
 */
define('config', 'config');
define('db', 'db');

session_start();