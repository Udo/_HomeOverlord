<?

  $GLOBALS['profiler_start'] = microtime();
  
  chdir(dirname(__FILE__));
  chdir('..');
  
  require('lib/h2genlib.php');
  require('lib/h2config.php'); 
  require('config/defaults.php'); 
  require('config/settings.php'); 

  header('content-type: text/javascript; charset=utf-8');

  //get the last-modified-date of this very file
  $lastModified=filemtime(__FILE__);
  //get a unique hash of this file (etag)
  $etagFile = md5_file(__FILE__);
  //get the HTTP_IF_MODIFIED_SINCE header if set
  $ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
  //get the HTTP_IF_NONE_MATCH header if set (etag: unique file hash)
  $etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
  
  //set last-modified header
  header("Last-Modified: ".gmdate("D, d M Y H:i:s", $lastModified)." GMT");
  //set etag-header
  header("Etag: $etagFile");
  //make sure caching is turned on
  header('Cache-Control: public');
  
  //check if page has changed. If not, send 304 and exit
  if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified || $etagHeader == $etagFile)
  {
         header("HTTP/1.1 304 Not Modified");
         exit;
  }
  
  ob_start("ob_gzhandler");

  foreach(array(
    'jquery-1.9.1.min.js',
    'masonry.js',
    'jslib.js',
    ) as $fle) 
    {
      print('//'.$fle.' 
      
      
      ');
      include('lib/'.$fle);
    }
