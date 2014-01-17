<script type="text/javascript" language="javascript" src="ext/DataTables/media/js/jquery.dataTables.js"></script>
<style type="text/css" title="currentStyle">
			@import "ext/DataTables/media/css/jquery.dataTables.css";
		</style>
<?

class H2Table 
{
  function __construct($props = array())
  {
    $this->props = $props;
    if(!isset($this->props['table'])) die('H2Table constructor expects table name parameter');
  }

  function makeAutoColumns()
  {
    foreach($this->fields as $k => $f)
    {
      $this->props['cols'][$k] = array(
        'type' => $f['Type'],
        'caption' => $f['Field'],
        );
    }
  }

  function prepareFieldProperties()
  {
    foreach($this->fields as $f)
    {
      if($f['Key'] == 'PRI')
        $this->props['key'] = $f['Field'];
    }
    return($this);
  }

  function getData()
  {
    $this->fields = o(db)->fields($this->props['table']);
    $this->prepareFieldProperties();
    
    if(sizeof($this->props['cols']) == 0)
      $this->makeAutoColumns();
       
    $this->data = o(db)->get('SELECT * FROM ?', array('$'.$this->props['table']));
       
    return($this);
  }

  function display()
  {
    ?><table class="datatable" id="table1">
      <thead>
        <?
        foreach($this->props['cols'] as $ck => $cprop)
        {
          ?><th><?= htmlspecialchars(first($cprop['caption'], $ck)) ?></th><?
        }
        ?>
      </thead>
      <tbody>
        <?
        foreach($this->data as $dRow)
        {
          ?><tr><?
          
          foreach($this->props['cols'] as $ck => $cprop)
          {
            ?><td><?
            if($cprop['onDisplay'])
              print($cprop['onDisplay']($ck, $dRow));
            else
              print(htmlspecialchars($dRow[$ck])); 
            ?></td><?
          }
          
          ?></tr><?
        }
        ?>
      </tbody>
    </table><?
    return($this);
  }

}

?><script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('.datatable').dataTable();
			} );
		</script>