<?
header('content-type: text/plain');

$nodeProc = explode(chr(10), shell_exec('ps aux | grep node'));

$srvFound = false;
foreach($nodeProc as $p)
{
  if(stripos($p, 'node srv.js') !== false) $srvFound = true;
}

if(!$srvFound)
{
  chmod('log/node.log', 0777);
  print('server process not found - starting...'.chr(10));
  print(shell_exec('cd '.$GLOBALS['APP.BASEDIR'].';/bin/sh node/srv.sh 2>&1'));
}