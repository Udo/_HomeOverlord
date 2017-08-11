<?
  
  class H2EventManager 
  {
    
    static function getEventsByDevice($deviceDS)
    {
      $searchPatterns = array(
        $deviceDS['d_bus'].'-ANY%',
        $deviceDS['d_bus'].'-ANY%',
        $deviceDS['d_bus'].'-'.$deviceDS['d_id'].'%',
        $deviceDS['d_bus'].'-'.$deviceDS['d_id'].'%',
        );
      $where = array(
        'e_address LIKE ?',
        'e_address_rev LIKE ?',
        'e_address LIKE ?',
        'e_address_rev LIKE ?',
        );
      $matches = o(db)->get('SELECT * FROM #events WHERE
        '.implode(' OR ', $where), $searchPatterns);
      return($matches);
    }
    
    static function getEmittableEventsByDevice($deviceDS)
    {
      $paramSet = array();
      if($deviceDS['d_bus'] == 'HM') 
      {
        foreach(HMRPC('getParamsetDescription', array($deviceDS['d_id'], 'VALUES')) as $dk => $dp)
        {
          $isEvent = ($dp['OPERATIONS'] & 4);
          if($isEvent) 
          {
            $dp['EXTTYPE'] = $dp['TYPE'];
            if($dp['TYPE'] == 'ENUM')
            {
              $exp = array();
              foreach($dp['VALUE_LIST'] as $ek => $ev)
                if($ev) $exp[] = $ek.'='.$ev;
              $dp['EXTTYPE'] = implode(', ', $exp);
            }
            $paramSet[$dk] = $dp;
            if($dk == 'PRESS_SHORT') $paramSet['PRESSED'] = $dp;
            if($dk == 'SET_TEMPERATURE') $paramSet['THERMOSTAT'] = $dp;
          }
        }
      }
      return($paramSet);
    }
    
    static function getEventsByTarget($deviceDS)
    {
      if($deviceDS['d_id'] == '')
        return(array());
      $where = array('e_code LIKE ?', 'e_code LIKE ?', 'e_code LIKE ?', );
      $searchPatterns = array(
        '%'.$deviceDS['d_id'].'%',
        '%'.@first($deviceDS['d_alias'], 'NOMATCH').'%',
        );
      return(o(db)->get('SELECT * FROM #events WHERE
        '.implode(' OR ', $where), $searchPatterns));
    }
    
    static function getApplicableEventsForDevice($deviceProperties)
    {
      $hdl = array(
        'ALL', 
        $deviceProperties['type'].'-ANY',
        $deviceProperties['type'].'-'.$deviceProperties['device'],
        $deviceProperties['type'].'-ANY-'.$deviceProperties['param'],
        $deviceProperties['type'].'-'.$deviceProperties['device'].'-'.$deviceProperties['param'],
        );
        
      if($deviceProperties['param'] == 'PRESS_SHORT' || $deviceProperties['param'] == 'PRESS_LONG_RELEASE')
        $hdl[] = $deviceProperties['type'].'-'.$deviceProperties['device'].'-PRESSED';
        
      if($deviceProperties['param'] == 'TEMPERATURE' || $deviceProperties['param'] == 'SET_TEMPERATURE')
      {
        $hdl[] = $deviceProperties['type'].'-'.$deviceProperties['device'].'-THERMOSTAT';
        $hdl[] = $deviceProperties['type'].'-ANY-THERMOSTAT';
      }
      
      return($hdl);
    }
    
  }