<?php
profile_point('page template start');

header('Content-Type: text/html; charset=utf-8');
header('X-Frame-Options: SAMEORIGIN');
header('X-UA-Compatible: chrome=1');

if(!o('user')->isLoggedIn())
  require('templates/menu.anonymous.php');
else
  require('templates/menu.user.php');

  
$mainMenu = array();
foreach($GLOBALS['menu'] as $m)
{
  $mainMenu[] = '<a class="'.($m['controller'] == $_REQUEST['controller'] ? 'selected' : '').'" href="'.
    actionUrl($m['action'], $m['controller']).'">'.l10n($m['text']).'</a>';
  if($m['controller'] == $_REQUEST['controller'] && sizeof($GLOBALS['submenu']) > 0)
    foreach($GLOBALS['submenu'] as $sm)
      $mainMenu[] = '<a class="indented '.($sm['action'] == $_REQUEST['action'] ? 'selected' : '').'" href="'.
        actionUrl($sm['action'], $sm['controller']).'">'.l10n($sm['text']).'</a>';
}

?><!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"> 
  <head>
    <title><?php echo htmlspecialchars(cfg('page/title', 'unnamed')).' &middot; '.cfg('service/name', 'UdoHome') ?></title>
    <script type="text/javascript" src="<?= cfg('service/subdir') ?>/lib/js.php?v=3"></script>   
    <link type="text/css" id="css_theme" rel="stylesheet" href="<?= cfg('service/subdir') ?>/themes/default/all.css.php?v=3&scheme=<?= getSunStatus() ?>"/> 
    <link rel="stylesheet" href="<?= cfg('service/subdir') ?>/themes/default/font-awesome/css/font-awesome.min.css">
		<meta http-equiv="X-UA-Compatible" content="chrome=1"/>
		<link rel="icon" href="icons/kfm_home.png" type="image/x-icon" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body class="<?= cfg('content/class') ?>">
  
    <div id="menu">
      <?= implode('', $mainMenu) ?>
    </div>
  
    <div id="header_outer">
      <div id="header">
        <a href="<?= actionUrl('index', 'home') ?>">▶ &nbsp;
          <span id="lefthdr">
            Home Control <?= $GLOBALS['pagetitle'] ?>
          </span><span id="lefthdr2"></span>
        </a>
      </div>
    </div>
	  
    <div id="content_outer">
      <div id="msgheader"></div>
      <div id="content">
      <table width="100%">
        <tr>
          <td width="*" valign="top">
            <?php echo $GLOBALS['content']['startuperrors'].$GLOBALS['content']['main'].$GLOBALS['content']['addendum'] ?>
          </td>        
        </tr>
      </table>
      </div>
    </div>
    
    <footer>
    </footer>
    
    <script>
    
      wsConnect();
    
      menuVisible = false;
      
      toggleMenu = function() {
        if(!menuVisible)
          $('#menu').fadeIn('normal');
        else
          $('#menu').fadeOut('normal');
        menuVisible = !menuVisible;
      };
      
      sunSet = <?= getSunset() ?>;
      /* sunset <?= date('H:i:s', getSunset(false)) ?> */
      sunRise = <?= getSunrise() ?>;
      /* sunrise <?= date('H:i:s', getSunrise(false)) ?> */
      currentRefMinutes = <?= dateToMinutes(time()) ?>;
      /* current <?= date('H:i:s', time()) ?> */
      currentSunState = '<?= getSunStatus() ?>';

      // switches the themes for night and day time
      if('<?= $_REQUEST['scheme'] ?>' == '')
      setInterval(function() {
      
        var dt = new Date();
        var currentMinutes = dt.getMinutes()+dt.getHours()*60;
        var sunState = '';

        if(currentMinutes > sunSet || currentMinutes < sunRise) sunState = 'night'; else sunState = 'day';
                
        if(sunState != currentSunState) {
          currentSunState = sunState;
          $('#css_theme').attr('href', '<?= cfg('service/subdir') ?>/themes/default/all.css.php?scheme='+sunState);
        }
      
        }, 5*1000);
      
    </script>
  </body>
<? if(cfg('debug')) { ?>
<!--
<?
  profile_point('page template end');
?>
<?= implode("\n", $GLOBALS['profiler_log']); ?> 
RAM usage: <?= ceil(memory_get_peak_usage()/1024) ?> kBytes 
uname: <?= php_uname() ?> 
pid: <?= getmypid() ?> user: <?= get_current_user() ?> 
src files: <?= implode(', ', preg_replace("/\/.*\//", "", get_included_files())) ?> 
-->
<? } ?>
</html>