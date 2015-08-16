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
  
  static function getAdminLinks()
  {
    $items = array();
    $items[] = array('icon' => 'reorder', 'title' => l10n('devices.show'), 'url' => actionUrl('show', 'devices'));
    $items[] = array('icon' => 'sitemap', 'title' => l10n('devices.groups'), 'url' => actionUrl('groups', 'devices'));
    $items[] = array('icon' => 'flash', 'title' => l10n('events.index'), 'url' => actionUrl('index', 'events'));
    $items[] = array('icon' => 'refresh', 'title' => l10n('devices.pairhm'), 'url' => actionUrl('pairhm', 'devices'));
    $items[] = array('icon' => 'refresh', 'title' => l10n('devices.pairhe'), 'url' => actionUrl('pairhe', 'devices'));
    $items[] = array('icon' => 'terminal', 'title' => l10n('devices.cli'), 'url' => actionUrl('cli', 'devices'));
    $items[] = array('icon' => 'dot-circle-o', 'title' => l10n('devices.modes'), 'url' => actionUrl('modes', 'devices'));
    $items[] = array('icon' => 'tablet', 'title' => l10n('devices.clients'), 'url' => actionUrl('clients', 'devices'));
    return($items);
  }
  
  static function getMenuLinks()
  {
    $items = array();
    $items[] = array('icon' => 'building-o', 'title' => l10n('devices.index'), 'url' => actionUrl('index', 'devices'));
    $items[] = array('icon' => 'hand-o-down', 'title' => l10n('events.manual'), 'url' => actionUrl('manual', 'events'));
    if(cfg('cameras'))
      $items[] = array('icon' => 'video-camera', 'title' => l10n('cam.index'), 'url' => actionUrl('index', 'cam'));
    return($items);
  }
  
  static function getUserMenu() 
  {
    foreach(H2Configuration::getMenuLinks() as $item)
      $submenu[] = '<a href="'.$item['url'].'">'.$item['title'].'</a>';
    return('<div class="submenu2">'.implode(' | ', $submenu).'</div>');
  }
  
  static function getAdminMenu() 
  {
    foreach(H2Configuration::getAdminLinks() as $item)
      $submenu[] = '<a href="'.$item['url'].'">'.$item['title'].'</a>';
    return('<div class="submenu2">'.implode(' | ', $submenu).'</div>');
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

