<?

$gds = o(db)->getDS('groups', $_REQUEST['id']);

$deviceConfig = array();
if($gds['g_deviceconfig'] != '')
  $deviceConfig = json_decode($gds['g_deviceconfig'], true);

$members = array();
foreach(explode(',', $gds['g_members']) as $m)
  $members[$m] = true;

$GLOBALS['pagetitle'] = 'Device Group #'.$gds['g_key'];

$states = explode(',', first($gds['g_states'], 'off,on'));

if(isset($_POST['id']))
{
  $saveForm = true;
  $gds['g_name'] = $_POST['g_name'];
  $gds['g_states'] = $_POST['g_states'];
}

?><form method="post" action="?">
  <input type="hidden" name="controller" value="devices"/>
  <input type="hidden" name="action" value="group"/>
  <input type="hidden" name="id" value="<?= $gds['g_key'] ?>"/>
  
  <div>
    Group Name:
    <input type="text" name="g_name" value="<?= htmlspecialchars($gds['g_name']) ?>"/>
    States:
    <input type="text" name="g_states" value="<?= htmlspecialchars(implode(',', $states)) ?>"/>
    <input type="submit" name="s" value="Save"/>
  </div>
  
  <br/>
  
  <table>
  <tr>
    <th colspan="2">Device</th>
    <?
    foreach($states as $state)
    {
      ?><th><?= $state ?></th><?
    }
    ?>    
  </tr>
  
  <?
  
  
  foreach($this->devices as $dc => $dcg)
  {
    ?><tr><td colspan="100"><div style="color:gray"><?= htmlspecialchars($dc) ?></div></td></tr><?
    foreach($dcg as $d)
    {
      ?><tr>
        <td>#<?= htmlspecialchars($d['d_id'])?></td>
        <td><?= htmlspecialchars($d['d_name'])?></td>
        <?
        foreach($states as $state)
        {
          if($saveForm)
          {
            $deviceConfig[$d['d_key']][$state] = $_POST['d'.$d['d_key'].'_'.$state];
          }
          ?><td><input type="text" size="4" name="d<?= $d['d_key'] ?>_<?= $state ?>" value="<?= htmlspecialchars($deviceConfig[$d['d_key']][$state]) ?>"/></td><?
        }
        ?>
      </tr><?
    }
  }
  
  ?>
  </table>
  
</form>

<?

if($saveForm)
{
  $gds['g_deviceconfig'] = json_encode($deviceConfig);
  o(db)->commit('groups', $gds);
  ?><div class="popupmsg">your changes have been saved<div>
  <script>
    setTimeout("$('.popupmsg').fadeOut('slow');", 1000);
  </script><?
}

?><style>

.d_active { color: #ff0; }
.d_inactive {  }

</style>
<script>

</script>