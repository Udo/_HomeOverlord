<?= $this->_getSubmenu2() ?>
<table class="devicetable"><?

  foreach(o(db)->get('SELECT * FROM devices ORDER BY d_room,d_name') as $ds) 
  {
    ?><tr>
      <td>#<?= $ds['d_key'] ?></td>
      <td><?= $ds['d_bus'].':'.$ds['d_id'] ?></td>
      <td><?= $ds['d_alias'] ?></td>
      <td><?= $ds['d_type'] ?></td>
      <td><?= $ds['d_room'] ?></td>
      <td><?= $ds['d_name'] ?></td>
      <td><?
      
      if($ds['d_bus'] == 'HM')
        print('<a href="'.actionUrl('params', 'devices', array('key' => $ds['d_key'])).'">Parameters</a>');
      
      ?></td>
    </tr><? 
  }

?></table>