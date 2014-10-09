<?php

function critical($err)
{
  WriteToFile('log/error.log', 'MySQL Error: '.$err.chr(10));
  die($err);
}

function so($s)
{
  return(mysql_real_escape_string($s));
}

function db() 
{
  if(!$GLOBALS['dbInstance']) $db = new H2Database();
  return($GLOBALS['dbInstance']);
}

class H2Database
{

  function __construct()
  {
    $GLOBALS['dbInstance'] = $this;
  }
  
  function connect()
  {
    if($GLOBALS['db_link']) return;
    profile_point('H2Database:connect() start');
    $GLOBALS['db_link'] = mysql_pconnect(cfg('db/host'), cfg('db/user'), cfg('db/password')) or
      critical('The database connection to server '.cfg('db/user').'@'.cfg('db/host').' could not be established (code: '.@mysql_error($GLOBALS['db_link']).')');
    mysql_select_db(cfg('db/database'), $GLOBALS['db_link']) or
      critical('The database connection to database '.cfg('db/database').' on '.cfg('db/user').'@'.cfg('db/host').' could not be established. (code: '.@mysql_error($GLOBALS['db_link']).')');
    if(mysql_client_encoding() != 'utf8') mysql_set_charset('utf8');
    profile_point('H2Database:connect() done');
  }
    
  // get a list of datasets matching the $query
  function get($query, $parameters = null, $opt = array())
  {
    $result = array();
    $error = '';
  
    $query = $this->parseQueryParams($query, $parameters);
  
    $lines = mysql_query($query, $GLOBALS['db_link']) or critical(mysql_error($GLOBALS['db_link']).' {query: '.$query.' }');
  
    while ($line = mysql_fetch_array($lines, MYSQL_ASSOC))
    {
      if (isset($keyByField))
        $result[$line[$keyByField]] = $line;
      else
        $result[] = $line;
    }
    mysql_free_result($lines);

  	profile_point('DB_GetList('.substr($query, 0, 40).'...)');
    return $result;
  }
  
  function fields($tablename)
  {
    $fields = array();
    foreach($this->get('SHOW COLUMNS FROM '.$this->t($tablename)) as $field)
      $fields[$field['Field']] = $field;
    profile_point('DB_GetFields('.$tablename.')');
    return($fields);
  }  
  
  // gets a list of keys for the table
  function keys($otablename)
  {
    $tablename = $this->t($otablename);
    if(isset($GLOBALS['config']['dbinfo'][$otablename]['keys']))
      return($GLOBALS['config']['dbinfo'][$otablename]['keys']);
  
        $pk = Array();
        $sql = 'SHOW KEYS FROM `'.$tablename.'`';
        $res = mysql_query($sql, $GLOBALS['db_link']) or critical(mysql_error());
          
        while ($row = @mysql_fetch_assoc($res))
        {
          if ($row['Key_name']=='PRIMARY')
            array_push($pk, $row['Column_name']);
        }
        profile_point('H2Database::keys('.$tablename.') REBUILD KEY CACHE');
        return($pk);
  
    return $result;
  }
  
  // updates/creates the $dataset in the $tablename
  function commit($otablename, &$dataset, $options = array())
  {
    $tablename = $this->t($otablename);
    $keynames = $this->keys($tablename);
    $keyname = $keynames[0]; 
    
    unset($GLOBALS['dbdatatmp'][$otablename][$keyname]);
  		 
    $query='REPLACE INTO '.$tablename.' ('.H2Database::MakeNamesList($dataset).
        ') VALUES('.H2Database::MakeValuesList($dataset).');';
    
    mysql_query($query, $GLOBALS['db_link']) or critical(mysql_error().'{ '.$query.' }');
    $dataset[$keyname] = first($dataset[$keyname], mysql_insert_id($GLOBALS['db_link']));
    
    profile_point('H2Database::commit('.$tablename.', '.$dataset[$keyname].')');
    return $dataset[$keyname];
  }  
  
  function getDSMatch($table, $matchOptions, $fillIfEmpty = true, $noMatchOptions = array())
  {
    $where = array('1');
    if (!is_array($matchOptions))
      $matchOptions = stringParamsToArray($matchOptions);
    foreach($matchOptions as $k => $v)
      $where[] = '('.$k.'="'.$this->safe($v).'")';
    foreach($noMatchOptions as $k => $v)
      $where[] = '('.$k.'!="'.$this->safe($v).'")';
    $iwhere = implode(' AND ', $where);
  	$query = 'SELECT * FROM '.$this->t($table).
      ' WHERE '.$iwhere;
    $resultDS = $this->getDSWithQuery($query);
    if ($fillIfEmpty && sizeof($resultDS) == 0)
      foreach($matchOptions as $k => $v)
        $resultDS[$k] = $v;
    return($resultDS);
  }
  
  // from table $tablename, get dataset with key $keyvalue
  function getDS($tablename, $keyvalue, $keyname = '', $options = array())
  {
    if($keyvalue == '0') return(array());
    $fields = @$options['fields'];
    $fields = first($fields, '*'); 
    if (!$GLOBALS['db_link']) return(array());
  
    $this->checkTableName($tablename);
    if ($keyname == '')
    {
      $keynames = $this->keys($tablename);
      $keyname = $keynames[0];
    }
    
    $cache_entry = $tablename.':'.$keyname.':'.$keyvalue;
    
    if(isset($GLOBALS['dbdatatmp'][$cache_entry])) return($GLOBALS['dbdatatmp'][$cache_entry]);
  
    $query = 'SELECT '.$fields.' FROM '.$tablename.' '.$options['join'].' WHERE '.$keyname.'="'.H2Database::Safe($keyvalue).'";';
    $rs = mysql_query($query, $GLOBALS['db_link']) or critical(mysql_error($GLOBALS['db_link']).' { Query: "'.$query.'" }');
  
    if ($line = @mysql_fetch_array($rs, MYSQL_ASSOC))
    {
      mysql_free_result($rs);
      $GLOBALS['dbdatatmp'][$cache_entry] = $line;
  	  profile_point('DB_GetDataSet('.$tablename.', '.$keyvalue.')');
      return($line);    
    }
    else
      $result = array();
  
  	profile_point('DB_GetDataSet('.$tablename.', '.$keyvalue.') #fail');
    return $result;
  }  

  function remove($tablename, $keyvalue, $keyname = null)
  {
    $this->checkTableName($tablename);
    if ($keyname == null)
    {
      $keynames = $this->keys($tablename);
      $keyname = $keynames[0];
    }
    $rs = mysql_query('DELETE FROM '.$tablename.' WHERE '.$keyname.'="'.
      $this->safe($keyvalue).'";', $GLOBALS['db_link'])
        or critical(mysql_error($GLOBALS['db_link']).'{ '.$query.' }');
    profile_point('DB_RemoveDataset('.$tablename.', '.$keyvalue.')');
  }  

  // retrieve dataset identified by SQL $query
  function getDSWithQuery($query, $parameters = null)
  {
    $query = $this->parseQueryParams($query, $parameters);
  
    $rs = mysql_query($query, $GLOBALS['db_link'])
      or critical(mysql_error($GLOBALS['db_link']).'{ '.$query.' }');
  
  	if ($line = mysql_fetch_array($rs, MYSQL_ASSOC))
    {
      $result = $line;
      mysql_free_result($rs);
    }
    else
      $result = array();

  	profile_point('DB_GetDataSetWQuery('.$query.')');
    return $result;
  }
  
  // execute a simple update $query
  function query($query, $parameters = null)
  {
    $query = $this->parseQueryParams($query, $parameters);
    if (substr($query, -1, 1) == ';')
      $query = substr($query, 0, -1);
    $rs = mysql_query($query)
      or critical(mysql_error().'{ '.$query.' }');
  	profile_point('DB_Update('.$query.')');
  }  

  function tables()
  {
    $result = mysql_list_tables(cfg('db/database'), $GLOBALS['db_link']) or critical(mysql_error());
    $tableList = array();
    while ($row = mysql_fetch_row($result))
        $tableList[$row[0]] = $row[0];
    sort($tableList);
    return($tableList);
  }

  function commitDelayed($opname, $tablename, &$dataset, $options = array())
  {
    $GLOBALS['db']['updatequeue'][$opname] = array($tablename, $dataset, $options);
  }
  
  function executePendingCommits()
  {
    if(is_array($GLOBALS['db']['updatequeue'])) foreach($GLOBALS['db']['updatequeue'] as $q)
    {
      $this->commit($q[0], $q[1], $q[1]);
    }
  }

  // create a comma-separated list of keys in $ds
  function makeNamesList(&$ds)
  {
    $result = '';
    if (sizeof($ds) > 0)
      foreach ($ds as $k => $v)
      {
        if ($k!='')
          $result = $result.','.$k;
      }
    return substr($result, 1);
  }
  
  // make a name-value list for UPDATE-queries
  function makeValuesList(&$ds)
  {
    $result = '';
    if (sizeof($ds) > 0)
      foreach ($ds as $k => $v)
      {
        if ($k!='')
          $result = $result.',"'.H2Database::safe($v).'"';
      }
    return substr($result,1);
  }  
    
  function parseQueryParams($query, $parameters = null)
  {
    if ($parameters != null)
    {
      $pctr = 0;
      $result = '';
      for($a = 0; $a < strlen($query); $a++)
      {
        $c = substr($query, $a, 1);
        if ($c == '?')
        {
          if(substr($parameters[$pctr], 0, 1) == '$')
            $result .= '`'.$this->safe(substr($parameters[$pctr], 1)).'`';
          else if(substr($parameters[$pctr], 0, 1) == '!')
            $result .= $this->safe(substr($parameters[$pctr], 1));
          else
            $result .= '"'.$this->safe($parameters[$pctr]).'"';
          $pctr++;
        }
        else
          $result .= $c;
      }
    }
    else
      $result = $query;
      
    return(str_replace('#', cfg('db/prefix'), $result));
  }

  function safe($raw)
  {
    if(!isset($GLOBALS['db_link']))
      return(addslashes($raw));
    else
      return(mysql_real_escape_string($raw, $GLOBALS['db_link']));
  }

  function t($table)
  {
    $this->checkTableName($table);
    return($this->safe($table));
  }
  
  function checkTableName(&$table)
  {
  	$prefix = cfg('db/prefix');
    $l = strlen($prefix);
    if (substr($table, 0, $l) != $prefix)
      $table = $prefix.$table;
    return($table);
  }


}


?>