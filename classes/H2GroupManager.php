<?
  
  class H2GroupManager 
  {
    
    static function getGroupsByDevice($deviceDS)
    {
      $matchingGroups = array();
      
      foreach(o(db)->get('SELECT * FROM #nvstore WHERE nv_key LIKE "group/%" ORDER BY nv_key') as $grpData)
      {
        $gName = $grpData['nv_key']; CutSegment('/', $gName);
        $gList = json_decode($grpData['nv_data']);
        if(is_array($gList) && array_search($deviceDS['d_key'], $gList) !== false)
          $matchingGroups[] = $gName;
      }
      
      return($matchingGroups);
    }
    
    
  }