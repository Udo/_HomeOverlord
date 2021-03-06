<?php
  
  if($_REQUEST['delcmd'] == md5($ds['d_key'].$ds['d_id']))
  {
    db()->remove('devices', $ds['d_key']);
    ?><script>
      document.location.href = '/hc/?/devices/show';      
    </script><?
  }
  
?>

<?= $this->_getSubmenu2('show') ?>

<form action="?" method="post">
<input type="hidden" name="controller" value="<?= $_REQUEST['controller'] ?>"/>
<input type="hidden" name="action" value="<?= $_REQUEST['action'] ?>"/>
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<form action="<?= actionUrl('edit', 'devices') ?>" method="post">
<input type="hidden" name="key" value="<?= $_REQUEST['key'] ?>"/>
<table class="settingsTable">
  <tr>
    <td style="text-align:right">
      <span class="faint">Device</span>   
    </td>
    <td width="*">
      <?= htmlspecialchars(first($ds['d_name'], '(unnamed)')) ?>
    </td>
  </tr>
  <tr>
    <td style="text-align:right">
      <span class="faint">Device ID</span>   
    </td>
    <td width="*">
      <?= htmlspecialchars(first($ds['d_id'], '(unknown ID)')) ?>
    </td>
  </tr>
  <tr>
    <td style="text-align:right;">
      <span class="faint">Type</span>   
    </td>
    <td width="*">
      <?= first($dev['TYPE'], $ds['d_type']) ?>
      <span class="faint">ID</span> <?= first($ds['d_id'], '(none)') ?> 
      <span class="faint">Alias</span> <?= first($ds['d_alias'], '#' . $ds['d_key']) ?>  
      <span class="faint">Name</span> <?= first($ds['d_name'], '(none)') ?></div>
    </td>  
  </tr><?
  if ($ds['d_bus'] == 'HM')
  {
    ?><tr>
      <td style="text-align:right">
        <span class="faint"></span>   
      </td>
      <td width="*">
        <a href="<?= actionUrl('params', 'devices', array('key' => $ds['d_key'])) ?>">Edit HomeMatic Parameters</a>
      </td>
    </tr><?    
  }

print('<tr><td valign="top" style="text-align: right;"><span class="faint">Compound</span></td><td>' . implode(', ', $related) . '</td></tr>');

foreach ($editable as $fn => $fncap)
{
?><tr>
      <td style="text-align:right">
         <?= $fncap ?>
      </td>
      <td width="*">
        <input type="text" style="width: 100%" name="<?= $fn ?>" value="<?= htmlspecialchars(first($_POST[$fn], $ds[$fn])) ?>"/>
      </td>
    </tr><?
}

?>

<tr>
    <td style="text-align:right">       
    </td>
    <td width="*">
      <hr/>
      <input type="button" value="Delete Device" style="float:right"
        onclick="if(confirm('Are you sure you want to delete this device?')) 
          document.location.href=document.location.href+'&delcmd=<?= md5($ds['d_key'].$ds['d_id']) ?>';"/>
      <input type="submit" value="Save Changes"/>
    </td>
  </tr>

<tr>
  <td colspan="10"></td>
</tr>
<? if(sizeof($paramSet) > 0) { ?>
<tr>
  
  <td style="text-align:right">Events Emitted</td>
  <td><ul>
  <?
  
  foreach($paramSet as $k => $v)
  {
    ?><li>
      <a href="<?= actionUrl('edit', 'events', array(
        'eventaddress' => $ds['d_bus'].'-'.@first($ds['d_id']).'-'.$k
        )) ?>"><i class="fa fa-share-square-o"></i></a>
      <?= $k ?> (<?= $v['EXTTYPE'] ?>) </li><?
  }
  
  ?> 
  </ul></td>
  
</tr>
<? }
if(sizeof($eventList) > 0) { ?>
<tr>
  
  <td style="text-align:right">Event Binding</td>
  <td><ul>
  <?
  
  foreach($eventList as $evt)
  {
    ?><li><a href="<?= actionUrl('edit', 'events', array('id' => $evt['e_key'])) ?>"><?= $evt['e_address'].' / '.$evt['e_address_rev'] ?></a></li><?
  }
  
  ?> 
  </ul></td>
  
</tr>
<? } 
if(sizeof($eventTargetList) > 0) { ?>
<tr>
  
  <td style="text-align:right">Targeted By</td>
  <td><ul>
  <?
  
  foreach($eventTargetList as $evt)
  {
    ?><li><a href="<?= actionUrl('edit', 'events', array('id' => $evt['e_key'])) ?>"><?= $evt['e_address'].' / '.$evt['e_address_rev'] ?></a></li><?
  }
  
  ?> 
  </ul></td>
  
</tr>
<? } 
if(sizeof($groupList) > 0) { ?>
<tr>
  
  <td style="text-align:right">Groups</td>
  <td><ul>
  <?
  
  foreach($groupList as $group)
  {
    ?><li><a href="<?= actionUrl('group', 'devices', array('id' => $group)) ?>"><?= $group ?></a></li><?
  }
  
  ?> 
  </ul></td>
  
</tr>
<? } ?>
<tr>
  
  <td style="text-align:right">Log</td>
  <td><pre style="max-width:600px;font-size: 85%;overflow:auto;"><?
  
  if($ds['d_id'] != '')
    print(shell_exec('tail -n 500 log/node.log | grep "'.$ds['d_id'].'" | tail'));
  else
    print('(no device id specified)');
  
  ?></pre></td>
  
</tr>


</table></form>

