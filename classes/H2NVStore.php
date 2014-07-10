<?php

class H2NVStore
{

  function get($key)
  {
    $ds = db()->getDS('nvstore', $key);
    if(sizeof($ds) == 0)
      return(array());
    else
      return(json_decode($ds['nv_data'], true));
  }
  
  function set($key, $data)
  {
    $ds = array(
      'nv_key' => $key,
      'nv_data' => json_encode($data, true),
      'nv_lastupdate' => time(),
      );
    db()->commit('nvstore', $ds);
  }

}