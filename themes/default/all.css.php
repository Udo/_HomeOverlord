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

function css_color($b, $factor = 1, $opacity = 1)
{
  $result = array();
  foreach($b as $c)
    $result[] = max(0, min(255, round($c * $factor)));
  $result[] = $opacity;
  return('rgba('.implode(', ', $result).')');    
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

switch($_REQUEST['scheme'])
{
  case('day'): 
  {
    $b = array(0x66, 0x55, 0x44);
    $background = '#fed';
    $barBackground = css_color($b);
    $bgBase = $b[0].','.$b[1].','.$b[2].'';
    $barText = $background;
    $onColor = '#06d';
    $timeCSS = 'day.css';
    break;
  }
  default:
  {
    $b = array(0x80, 0xa0, 0xff);
    $background = '#000';
    $barBackground = css_color($b, 0.5, 0.5);
    $bgBase = '0,51,102';
    $bgBase = $b[0].','.$b[1].','.$b[2].'';
    $onColor = '#ee7';
    $barText = css_color($b, 1.2);
    $timeCSS = 'night.css';
    break;
  }
}

$colWidth = 270;
$menuWidth = 140;

$baseColor = css_color($b);
$textColor = css_color($b);
$lighterColor = css_color($b, 1.30);
$lighterColorHighlight = css_color($b, 1.70);
$washedOutColor = css_color($b, 1.70);
$darkerColor = css_color($b, 1, 0.25);
$linkColor = $baseColor;
$stdMargin = '6px';
$bigMargin = '16px';
$stdPadding = '6';
$bgAttention = '#ffc';
$borderColor = '1px solid '.$darkerColor;
$lightBorderColor = '1px solid '.$darkerColor;
$borderBanner = '1px solid '.$darkerColor;

include('default.css');
include('masonry.css');
include($timeCSS);
?>