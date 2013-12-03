<?

foreach($this->devices as $d)
{
  $di = array();
  foreach($d as $k => $v)
    $di[] = $k.'='.$v;
  
  ?><div><?= implode(', ', $di) ?></div><?
}

?>