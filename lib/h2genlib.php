<?php
/*
 * Hubbub utility library
 * Purpose: contains a few useful general functions
 */
$GLOBALS['profiler_last'] = first($GLOBALS['profiler_start'], microtime());
define('URL_CA_SEPARATOR', '-');

/*
 * the o() function is a utility that can be used for some neat shorthand code
 */
function o($object = null, $objectName = null)
{
  if(is_object($object))
  {
    if(is_string($objectName))
      $GLOBALS['o'][$objectName] = &$object;
    return($object);
  }
  else if(is_string($object))
  {
    return($GLOBALS['o'][$object]);
  }
}

/* parses an HTTP result and returns it in a nicely filtered array:
   'length' : the size of the response
   'result' : HTTP numeric result code
   'headers' : array with the response headers
   'data' : if any json data was in the response body
   'body' : the actual response data */
function http_parse_request($result, $headerMode = true)
{
  $resHeaders = array();
  $resBody = array();
  
  foreach(explode("\n", $result) as $line)
  {
    if($headerMode)
    {
      if(strStartsWith($line, 'HTTP/'))
      {
        $httpInfoRecord = explode(' ', trim($line));
        if($httpInfoRecord[1] == '100') $ignoreUntilHTTP = true;
        else 
        {
          $ignoreUntilHTTP = false;
          $resHeaders['code'] = $httpInfoRecord[1];
          $resHeaders['HTTP'] = $line;
        }
      }
      else if(trim($line) == '')
      {
        if(!$ignoreUntilHTTP) $headerMode = false;
      }
      else 
      {
        $hdr_key = trim(CutSegment(':', $line));
        $resHeaders[strtolower($hdr_key)] = trim($line); 
      }
    }
    else
    {
      $resBody[] = $line; 
    }    
  }

  $body = trim(implode("\n", $resBody));
  $data = json_decode($body, true);

  return(array(
    'length' => strlen($body),
    'result' => $resHeaders['code'],
    'headers' => $resHeaders,
    'data' => $data,
    'body' => $body));
}

/* another convenience function, test if a string starts with another */
function strStartsWith($haystack, $needle)
{
  return(substr(strtolower($haystack), 0, strlen($needle)) == strtolower($needle));
}

/* test whether a string ends with another */
function strEndsWith($haystack, $needle)
{
  return(substr(strtolower($haystack), -strlen($needle)) == strtolower($needle));
}

/* cut $cake at the first occurence of $segdiv, returns the slice 
   if $segdiv is an array, it will use the first occurence that matches any of its entries */
function CutSegmentEx($segdiv, &$cake, &$found, $params = array())
{
  if(!is_array($segdiv)) $segdiv = array($segdiv);
  $p = false;
  foreach($segdiv as $si)
  {  
    $pi = strpos($cake, $si);
    if($pi !== false && ($pi < $p || $p === false)) 
    {
      $p = $pi;
      $pfirst = $p;
      $slen = strlen($si);
    }
  }
  if ($p === false)
  {
    $result = $cake;
    $cake = '';
    $found = false;
  }
  else
  {
    if($params['full']) $pfirst += $slen;
    $result = substr($cake, 0, $pfirst);
    $cake = substr($cake, $p + $slen);
    $found = true;
  }
  return $result;
}

/* like CutSegmentEx(), but doesn't carry the $found result flag */
function CutSegment($segdiv, &$cake, $params = array())
{
  return(CutSegmentEx($segdiv, $cake, $found, $params));
}



/* this is for making multiple simultaneous requests, takes an array of URLs instead of only one */
// fixme: should probably be unified with cqrequest()
function cqrequest($rq_array, $post = array(), $timeout = 10, $headerMode = true, $onlyHeaders = false)
{
  $rq = array();
  $content = array();
  $active = null;
  $idx = 0;
  $multi_handler = curl_multi_init();
  
  if(!is_array($rq_array)) $rq_array = array(array('url' => $rq_array));
  
  // configure each request
  foreach($rq_array as $rparam) if(trim($rparam['url']) != '')
  {
    profile_point('cqrequest('.substr($rparam['url'], 0, 64).'...)');
    $idx++;
    $channel = curl_init();
    curl_setopt($channel, CURLOPT_URL, $rparam['url']);
    $combinedParams = $post;
    if(is_array($rparam['params'])) $combinedParams = array_merge($rparam['params'], $post);
    if(sizeof($combinedParams)>0) 
    {
      curl_setopt($channel, CURLOPT_POST, 1); 
      curl_setopt($channel, CURLOPT_POSTFIELDS, $combinedParams);
    }
    curl_setopt($channel, CURLOPT_HEADER, 1); 
    curl_setopt($channel, CURLOPT_TIMEOUT, $timeout); 
    curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
    curl_multi_add_handle($multi_handler, $channel);
    $rq[$idx] = array($channel, $rparam);
  }
  
  if(sizeof($rq) == 0) return(array());
  
  // execute
  do {
      $mrc = curl_multi_exec($multi_handler, $active);
  } while ($mrc == CURLM_CALL_MULTI_PERFORM);
  
  // wait for return
  while ($active && $mrc == CURLM_OK) {
    if (curl_multi_select($multi_handler) != -1) {
        do {
            $mrc = curl_multi_exec($multi_handler, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    }
  }
  
  // cleanup
  foreach($rq as $idx => $rparam)
  {
    $result = http_parse_request(curl_multi_getcontent($rparam[0]));
    $result['param'] = $rparam[1];
    $content[$idx] = $result;
    curl_multi_remove_handle($multi_handler, $channel);
    $lastIdx = $idx;
  }
  
  curl_multi_close($multi_handler);
  
  if(sizeof($content) == 1) $content = $content[$lastIdx];

  profile_point('cqrequest() done');
  
  return($content);  
}

/* returns the first non-null, non-empty variable passed to it */
function first()
{
	foreach(func_get_args() as $a)
		if($a != null && $a != '') return($a);
	return('');
}

/* makes an input totally safe by only allowing a-z, 0-9, and underscore (might not work correctly) */
function safeName($raw)
{
	return(preg_replace('/[^a-z|0-9|\_|\.]*/', '', strtolower($raw)));
}

/* makes a commented profiler entry */ 
function profile_point($text)
{
  $thistime = microtime();
  $GLOBALS['profiler_log'][] = profiler_microtime_diff($thistime, $GLOBALS['profiler_start']).' | '.profiler_microtime_diff($thistime, $GLOBALS['profiler_last']).' msec | '.
    ceil(memory_get_usage()/1024).' kB | '.$text;
  $GLOBALS['profiler_last'] = $thistime;
}

/* subtracts to profiler timestamps and returns miliseconds */
function profiler_microtime_diff(&$b, &$a)
{
  list($a_dec, $a_sec) = explode(" ", $a);
  list($b_dec, $b_sec) = explode(" ", $b);
  return number_format(1000*($b_sec - $a_sec + $b_dec - $a_dec), 3);
}

/* append any string to the given file */
function WriteToFile($filename, $content)
{
  $open = fopen($filename, 'a+');
  fwrite($open, $content);
  fclose($open);
}

function logError($msg)
{
  die($msg);
  WriteToFile('log/error.log', 
    gmdate('Y-m-d H:i:s').chr(9).
    $_SERVER['REMOTE_ADDR'].chr(9).
    $msg."\n\r");
}

/* loads a language file */
function load_l10n($path, $lang0 = null)
{
  if($lang0 == null)
    $lang0 = cfg('language', 'en');
  $fileNames[] = 'mvc/'.$path.'l10n.'.$lang0.'.json';
  #$fileNames[] = 'custom/'.$path.'l10n.'.$lang0.'.json';
  if($lang0 != 'en')
  {
    // default to english if other language file not present
    $fileNames[] = 'mvc/'.$path.'l10n.en.json';
    #$fileNames[] = 'custom/'.$path.'l10n.en.json';
  }
  foreach($fileNames as $fn)
  {
    if(file_exists($fn))
    {
      foreach(json_decode(file_get_contents($fn), true) as $k => $v)
        $GLOBALS['l10n'][$k] = $v;
      return(true);
    }
  }
}

/* localization function. supply the ID string, it returns the localized string. */
function l10n($s, $silent = false)
{
  $lout = $GLOBALS['l10n'][$s];
  if(isset($lout)) 
    return($lout);
  else if($silent === true)
    return('');
  else
    return('['.$s.']');
}

/* internal function needed to parse parameters in the form of "p1=bla,p2=blub" into a proper array */
function stringParamsToArray($paramStr)
{
  $result = array();
  foreach(explode(',', $paramStr) as $line)
  {
    $k = CutSegment('=', $line);
    $result[$k] = $line;	
  }
  return($result);
}

/* makes an URL calling a specific controller with a specific action */
function actionUrl($action = null, $controller = null, $params = array())
{ 
  if (!is_array($params)) $params = stringParamsToArray($params);
  if($action == null) $action = $_REQUEST['action'];
  if($controller == null) $controller = $_REQUEST['controller'];
  if(cfg('service/url_rewrite'))
  {
    return(cfg('service/subdir').'/'.urlencode($controller).URL_CA_SEPARATOR.urlencode($action).
      (sizeof($params) > 0 ? '?'.http_build_query($params) : ''));
  }
  else
  {
    $params['action'] = $action;
    $params['controller'] = $controller;
    return(cfg('service/subdir').'/?'.http_build_query($params));
  }
}

