<?php

$dev = new H2HALDevice(2024);

print('<pre>');

print_r($dev->state('closed', 'because test'));

print('<hr/>');

print_r($dev);

print('</pre>');
