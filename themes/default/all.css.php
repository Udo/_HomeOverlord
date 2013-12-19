<?
/**
 * Author: udo.schroeter@gmail.com
 * Project: Hubbub2
 * Description: combines CSS files for output to optimize browser loading
 */

header('content-type: text/css; charset=UTF-8');
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s", time()+60*60*4) . " GMT");
ob_start("ob_gzhandler");
error_reporting(E_ERROR | E_PARSE);

// this is also defined in the main config, ugly redundancy for the sake of speed ;-)

function dechex2($a)
{
  $result = '';
  if($a > 255) $a = 255; else if($a < 0) $a = 0;
  $result = dechex($a);
  if(strlen($result) < 2) $result = '0'.$result;
  return($result);
}

function css_color($b, $lightenBy = 0)
{
  $result = '';
  foreach($b as $c)
    $result .= dechex2($c + $lightenBy);
  return('#'.$result);    
}

function css_gradient($c1, $c2, $defaultColor = null)
{
  if($defaultColor == null) $defaultColor = $c1;
  return("
  background: $defaultColor;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='$c2', endColorstr='$c1'); 
  background: -webkit-gradient(linear, left top, left bottom, from($c2), to($c1)); 
  background: -moz-linear-gradient(top,  $c2,  $c1); ");
}

function css_rounded_borders($size)
{
  return('  -moz-border-radius: '.$size.';
    -webkit-border-radius: '.$size.';
    border-radius: '.$size.';');
}

function css_box_shadow($color = 'aaa', $size = 5)
{
  $boxShadowDef = '0px 0px '.$size.'px #'.$color;
  return('  box-shadow: '.$boxShadowDef.';
    -moz-box-shadow: '.$boxShadowDef.';
    -webkit-box-shadow: '.$boxShadowDef.';');
}

/*$colorSchemes = array(
  'default'  => array('basecolor' => array(0x80, 0xa0, 0xff), 'linkcolor' => 0),
  'green'    => array('basecolor' => array(0x00, 0x90, 0x00), 'linkcolor' => -50),
  'orange'   => array('basecolor' => array(0xFF, 0x60, 0x00), 'linkcolor' => -50),
  'gray'     => array('basecolor' => array(0x80, 0x80, 0x80), 'linkcolor' => -50),
  'pink'     => array('basecolor' => array(0xCC, 0x50, 0xCC), 'linkcolor' => -50),
  'graphite' => array('basecolor' => array(0x60, 0x70, 0x90), 'linkcolor' => -50),
  'blue'     => array('basecolor' => array(0x00, 0x40, 0xA0), 'linkcolor' => 0),
  );
*/

switch($_REQUEST['scheme'])
{
  case('day'): 
  {
    $b = array(0x66, 0x55, 0x44);
    $background = '#fed';
    $hlColor = '#000';
    $barBackground = css_color($b);
    $barBackground2 = '#cba';
    $textColor2 = '#876';
    $barText = '#fed';
    break;
  }
  default:
  {
    $b = array(0x80, 0xa0, 0xff);
    $background = '#000';
    $hlColor = '#ff0';
    $barBackground = '#036';
    $barBackground2 = '#024';
    $textColor2 = '#987';
    $barText = css_color($b, 20);
    break;
  }
}

$colWidth = 270;
$menuWidth = 140;

$baseColor = css_color($b, 0);
$textColor = css_color($b, 0);
$lighterColor = css_color($b, +30);
$lighterColorHighlight = css_color($b, +70);
$washedOutColor = css_color($b, +120);
$darkerColor = css_color($b, -100);
$lightGrayBackground = '#f6f6f6';
$linkColor = $baseColor;
$stdMargin = '6px';
$bigMargin = '16px';
$stdPadding = '6px';
$bgAttention = '#ffc';
$borderColor = '1px solid '.$darkerColor;
$lightBorderColor = '1px solid '.$darkerColor;
$borderBanner = '1px solid '.$darkerColor;

include('default.css');
include('masonry.css');

?>