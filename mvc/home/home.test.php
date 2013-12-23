<?php

$dev = new H2HALDevice(2049);

print('<pre>');

print('previous state: '.$dev->state().chr(10));

//print_r($dev->state($dev->state() == 'open' ? 'closed' : 'open', 'because test'));
print_r($dev->state('closed', 'because test'));

print('<hr/>');

print_r($dev);

print('</pre>');
