<?= $this->_getSubmenu2() ?>
<form action="?" method="post">
<input type="hidden" name="controller" value="<?= $_REQUEST['controller'] ?>"/>
<input type="hidden" name="action" value="<?= $_REQUEST['action'] ?>"/>
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<?

$doSave = isset($_POST['controller']);

$ds = o(db)->getDS('devices', $_REQUEST['key']);

  function showParam($val, $p)
  {
    switch($p['TYPE'])
    {
      case('BOOL'):
      {
        if($p['WRITABLE'])
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
    return('--');
  }
  
  function getOps($operationsVal)
  {
    if (($operationsVal & 1) == 1) $r[] = 'READ';
    if (($operationsVal & 2) == 2) $r[] = 'WRITE';
    if (($operationsVal & 4) == 4) $r[] = 'EVENT';
    return(implode(', ', $r));
  }

foreach(array('MASTER', 'VALUES') as $psetType)
{
  $saveP = array();
  $p = HMRPC('getParamset', array($ds['d_id'], $psetType));
  $pdes = HMRPC('getParamsetDescription', array($ds['d_id'], $psetType));
    
  ?><table width="100%" style="max-width: 600px;" class="border-bottom"><?
  foreach($pdes as $k => $ps)
  {
    $ps['WRITABLE'] = ($ps['OPERATIONS'] & 2) == 2;
    if($ps['WRITABLE'] && $doSave)
    {
      $fVal = $_POST[$k];
      if($fVal == 'Yes') $fVal = true;
      if($fVal == 'No') $fVal = false;
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
    print_r($saveP);
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