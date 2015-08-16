<?php 

// enter your database credentials here
$GLOBALS['config']['db'] = array(
    'host' => 'localhost',
    'user' => 'hc',
    'password' => 'UQM3DY6xnLscMxxa',
    'database' => 'hc',
  );

// enter your geo location here
$GLOBALS['config']['geo'] = array(
    'lat' => 49.635559,
    'long' => 8.35972,
    'zenith' => 90,
    'timezone' => 'Europe/Berlin',
    'city' => 'Worms, Germany',
  );
  
// your server's address
$GLOBALS['config']['service']['server'] = '10.32.0.10';
$GLOBALS['config']['service']['wserverurl'] = '10.32.0.10:1081';

$GLOBALS['config']['cameras'] = array(
  'cams' => array(
    array('photoUrl' => 'http://10.32.4.107:8080/photo.jpg', 'id' => 'cam01', 'title' => 'Attic', 'room' => 'Other'),
    ),
  );
  
$GLOBALS['config']['sensors'] = array(
  'sensors' => array(
    ),
  );