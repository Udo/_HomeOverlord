<?php

$dev = new H2HALDevice(2049);

print('<pre>');

print('previous state: '.$dev->state().chr(10));

//print_r($dev->state($dev->state() == 'open' ? 'closed' : 'open', 'because test'));
print_r($dev->state(first($_REQUEST['state'], 'open'), 'because test'));

print('<hr/>');

print_r($dev);

print('</pre>');
