<?php
  
$ds = array(
  'd_name' => 'New HomeEasy Device', 
  'd_bus' => 'HE',
  'd_id' => mt_rand(1000, 9999),
  'd_type' => 'Light',
  );

$key = o(db)->commit('devices', $ds);

header('location: '.actionUrl('edit', 'devices', array('key' => $key)));
die();