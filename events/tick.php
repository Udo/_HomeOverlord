<?php
  
  $handlerData['time'] = time();
  
  if(is_array($GLOBALS['config']['zwave']['gateways']))
    foreach($GLOBALS['config']['zwave']['gateways'] as $gw)
    {
      H2Zwave::RetrieveDeviceData($gw);      
      $data = H2Zwave::GetDeviceData($gw);
      $metaData = H2Zwave::GetDeviceMetaData($gw);
      H2Zwave::DeviceDataToDatabase($data, $metaData);      
    }
