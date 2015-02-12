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
    'lat' => 49.63837280000002,
    'long' => 8.328505900000007,
    'zenith' => 90,
    'timezone' => 'Europe/Berlin',
    'city' => 'Worms, Germany',
  );
  
// your server's address
$GLOBALS['config']['service']['server'] = '10.32.0.10';
$GLOBALS['config']['service']['wserverurl'] = '10.32.0.10:1081';

$GLOBALS['config']['cameras'] = array(
  'cams' => array(
    array('photoUrl' => 'http://10.32.4.109:8080/photo.jpg', 'id' => 'cam01', 'title' => 'Cat 1'),
    array('photoUrl' => 'http://10.32.4.103:8080/photo.jpg', 'id' => 'cam04', 'title' => 'Cat 2'),  
    array('photoUrl' => 'http://10.32.4.104:8080/photo.jpg', 'id' => 'cam02', 'title' => 'Attic', 'room' => 'Other'),  
    array('photoUrl' => 'http://10.32.4.110:8080/photo.jpg', 'id' => 'cam03', 'title' => 'Porch'),  
    ),
  );
  
$GLOBALS['config']['sensors'] = array(
  'sensors' => array(
    ),
  );