<?= $this->_getSubmenu2() ?>
<form action="?" method="post">
<input type="hidden" name="controller" value="<?= $_REQUEST['controller'] ?>"/>
<input type="hidden" name="action" value="<?= $_REQUEST['action'] ?>"/>
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<?

$doSave = isset($_POST['controller']);

$ds = o(db)->getDS('devices', $_REQUEST['key']);

?><h2><?= $ds['d_id'] ?> <?= first($ds['d_alias'], '#'.$ds['d_key']) ?> | <?= first($ds['d_name']) ?></h2><?

  $related = array();
  $idnr = $ds['d_id'];
  $idroot = CutSegment(':', $idnr);
  foreach(o(db)->get('SELECT d_key,d_id,d_alias,d_type FROM devices WHERE d_id LIKE "'.$idroot.'%" ORDER BY d_id') as $dds)
  {     
    $related[] = '<a href="'.actionUrl('params', 'devices', array('key' => $dds['d_key'])).'" style="'.($dds['d_key'] == $ds['d_key'] ? 'font-weight:bold;' : '').'">'.
      htmlspecialchars(first($dds['d_alias'], $dds['d_type'])).' '.$dds['d_id'].'</a>';//array($ds['d_id'] => $ds['d_id'].' ('.first($ds['d_alias'], $ds['d_id']).')');
  }

  print('Compound: '.implode(', ', $related).'<br/><br/>');

  function showParam($val, $p)
  {
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

foreach(array('MASTER', 'VALUES') as $psetType)
{
  $saveP = array();
  $p = HMRPC('getParamset', array($ds['d_id'], $psetType));
  profile_point('getParamset '.$psetType);
  $pdes = HMRPC('getParamsetDescription', array($ds['d_id'], $psetType));
  profile_point('getParamsetDescription '.$psetType);
    
  ?><table width="100%" style="max-width: 600px;" class="border-bottom"><?
  foreach($pdes as $k => $ps)
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
    
      <td width="240"><div align="right">
        <?= $k ?>
      </div></td>
      <td width="20%">
        <b><?= showParam($p[$k], $ps) ?></b> 
      </td>
      <td width="20%">
        (<?= $ps['MIN']+0 ?>-<?= $ps['MAX']+0 ?>)
      </td>
      <td width="20%">
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
  ?><!--<pre><?
  print_r($p);
  print_r($pdes);
  ?></pre>--><?
}



?><input type="submit" value="Save"/></form>