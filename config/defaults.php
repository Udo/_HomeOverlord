<?php 

$GLOBALS['config'] = array(
  'service' => array(
    'defaultcontroller' => 'home',
    'defaultaction' => 'index',
    'subdir' => '.',
    'server' => '10.32.0.10',
    'wsport' => '1081',
    'url_rewrite' => false,
  ),
  'hal' => array(
    'Light' => 'OnOff',
    'IT' => 'OnOff',
    'Blinds' => 'Blinds',
    'Key' => 'Sender',
    'MOTION_DETECTOR' => 'Motion',
    'MAINTENANCE' => 'Maintenance',
    ),
  'language' => 'en',
  'debug' => true,
  );
  
$GLOBALS['config']['dbinfo']['messages']['keys'] = array('m_key');

