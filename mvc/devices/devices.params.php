<?= $this->_getSubmenu2('show') ?>
<form action="?" method="post">
<input type="hidden" name="controller" value="<?= $_REQUEST['controller'] ?>"/>
<input type="hidden" name="action" value="<?= $_REQUEST['action'] ?>"/>
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<?
$doSave = isset($_POST['controller']);

$ds = getDeviceDS($_REQUEST['key']);
$dev = HMRPC('getDeviceDescription', array($ds['d_id']));
$_REQUEST['actionEvents'] = array();

?>
<table style="margin-top: -8px; margin-bottom: 12px; max-width: 800px; width: 100%;">
  <tr>
    <td style="text-align:right">
      <span class="faint">HomeMatic</span>   
    </td>
    <td width="*">
      <b><a href="<?= actionUrl('edit', 'devices', array('key' => $_REQUEST['key'])) ?>"><?= first($ds['d_name']) ?></a></b> 
      | <a href="<?= actionUrl('edit', 'devices', array('key' => $_REQUEST['key'])) ?>">edit</a>
    </td>
  </tr>
  <tr>
    <td style="text-align:right">
      <span class="faint">Type</span>   
    </td>
    <td width="*">
      <?= first($dev['TYPE'], $ds['d_type']) ?>
      <span class="faint">ID</span> <?= $ds['d_id'] ?> 
      <span class="faint">Alias</span> <?= first($ds['d_alias'], '#'.$ds['d_key']) ?>  
    </td>  
  </tr>
  <tr>
    <td style="text-align:right">
      <span class="faint">Direction</span>   
    </td>
    <td width="*">
      <?
        
        $dirText = array( 0 => 'None', 1 => 'Sender', 2 => 'Receiver' );
        print($dirText[$dev['DIRECTION']]);
        
      ?></div>
    </td>  
  </tr>
  <?
  $related = array();
  $idnr = $ds['d_id'];
  $idroot = CutSegment(':', $idnr);
  foreach(o(db)->get('SELECT d_key,d_id,d_alias,d_type FROM devices WHERE d_id LIKE "'.$idroot.'%" ORDER BY d_id') as $dds)
  {     
    $related[] = '<a href="'.actionUrl('params', 'devices', array('key' => $dds['d_key'])).'" style="'.($dds['d_key'] == $ds['d_key'] ? 'font-weight:bold;' : '').'">'.
      htmlspecialchars(first($dds['d_alias'], $dds['d_type'])).' '.$dds['d_id'].'</a>';//array($ds['d_id'] => $ds['d_id'].' ('.first($ds['d_alias'], $ds['d_id']).')');
  }

  print('<tr><td valign="top" style="text-align:right"><span class="faint">Compound</span></td><td>'.implode(', ', $related).'</td></tr></table>');

  function showParam($val, $p, $k)
  {
    global $actionEvents;
    switch($p['TYPE'])
    {
      case('BOOL'):
      {
        if($p['WRITABLE'] && $p['ID'] != 'AES_ACTIVE')
          return('<select name="'.$p['ID'].'"><option'.($val === true ? ' selected' : '').'>Yes</option><option'.($val != true ? ' selected' : '').'>No</option></select>');
        else
          return($val === true ? 'Yes' : 'No');
        break;
      }
      case('INTEGER'):
      {
        if($p['WRITABLE'])
          return('<input type="text" name="'.$p['ID'].'" value="'.$val.'"/>');
        else
          return($val);
        break;
      }
      case('FLOAT'):
      {
        if($p['WRITABLE'])
          return('<input type="text" name="'.$p['ID'].'" value="'.number_format($val, 3).'"/>');
        else
          return(number_format($val, 3));
        break;
      }
      case('ACTION'):
      {
        $_REQUEST['actionEvents'][] = $k;
        if($k == 'PRESS_SHORT') $_REQUEST['actionEvents'][] = 'PRESSED';
        return('-');
        break;
      }
    }
    return($val.' ('.$p['TYPE'].')');
  }
  
  function getOps($operationsVal)
  {
    if (($operationsVal & 1) == 1) $r[] = 'READ';
    if (($operationsVal & 2) == 2) $r[] = 'WRITE';
    if (($operationsVal & 4) == 4) $r[] = 'EVENT';
    return(implode(', ', $r));
  }
  
  function parseParam($val, $p)
  {
    switch($p['TYPE'])
    {
      case('BOOL'):
      {
        return($val == 'Yes' ? true : false);
        break;
      }
      case('INTEGER'):
      {
        return($val+0);
        break;
      }
      case('FLOAT'):
      {
        return($val + 0.0);
        break;
      }
    }
    return(0);
  }

foreach($dev['PARAMSETS'] as $psetType)
{
  $saveP = array();
  $p = HMRPC('getParamset', array($ds['d_id'], $psetType));
  profile_point('getParamset '.$psetType);
  $pdes = HMRPC('getParamsetDescription', array($ds['d_id'], $psetType));
  profile_point('getParamsetDescription '.$psetType);
    
  ?><table width="100%" style="max-width: 700px;" class="border-bottom"><?
  if(is_array($pdes)) foreach($pdes as $k => $ps)
  {
    $ps['WRITABLE'] = /*($psetType != 'VALUES') &&*/ ($ps['OPERATIONS'] & 2) == 2 && $k != 'AES_ACTIVE';
    if($ps['WRITABLE'] && $doSave)
    {
      $fVal = parseParam($_POST[$k], $ps);
      if($fVal != $p[$k]) 
      {
        $saveP[$k] = $fVal;
        $p[$k] = $fVal;
      }
    }
    ?><tr>
    
      <td width="200"><div align="right">
        <span class="faint"><?= $k ?></span>
      </div></td>
      <td width="200">
        <b><?= showParam($p[$k], $ps, $k) ?></b> 
      </td>
      <td width="80">
        (<?= $ps['MIN']+0 ?>-<?= $ps['MAX']+0 ?>)
      </td>
      <td width="*">
        <?= getOps($ps['OPERATIONS']) ?>
      </td>
    
    </tr><?
  }
  ?></table><?
  if(sizeof($saveP) > 0)
  {
    print('<pre>New parameters have been saved: ');
    print(json_encode($saveP));
    print_r(HMRPC('putParamset', array($ds['d_id'], $psetType, $saveP)));
    //print_r($_POST);
    print('</pre>');
  }
  /*?><pre><?
  print_r($pdes);
  ?></pre><?*/
}

/*  ?><pre><?
  print_r($dev);
  ?></pre><?
*/
?><input type="submit" value="Save"/></form><?


























?>