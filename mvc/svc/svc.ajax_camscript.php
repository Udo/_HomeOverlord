<?php
header('content-type: text/plain');
ob_start();

$camConfig = cfg('cameras');
$sensorConfig = cfg('sensors');

if(!file_exists('data/cam'))
  @mkdir('data/cam', 0775, true);
if(!file_exists('data/sensors'))
  @mkdir('data/sensors', 0775, true);
foreach($camConfig['cams'] as $cam) 
  if(!file_exists('data/timelapse/'.$cam['id']))
    @mkdir('data/timelapse/'.$cam['id'], 0775, true);

?>
cd "<?= $GLOBALS['APP.BASEDIR'].'/data/' ?>"

# polling camera photos

<?
foreach($camConfig['cams'] as $cam)
{
?>
curl -s -m 9 <?= $cam['photoUrl'] ?> > cam/<?= $cam['id'] ?>_full.jpg & 
<?
}
?>

# sensor data

<?
foreach($sensorConfig['sensors'] as $sen)
{
?>
curl -s -m 9 <?= $sen['jsonUrl'] ?> > sensors/<?= $sen['id'] ?>.json & 
<?
}
?>

# wait until all data should be saved
sleep 10
date_string=$(date +"%H-%M")

<? /*
function saveToArchive(){
  date_string=$(date +"%Y%m%d%H%M")
  mnth=$(date +"%Y%m%d")
  mkdir -p cam_archive/$1/
  cp cam/$1.jpg cam_archive/$1/$date_string.jpg
} */ ?>
# down-convert and enhance
<?
foreach($camConfig['cams'] as $cam) 
{
  if($cam['resize'])
  {
  ?>
  convert cam/<?= $cam['id'] ?>_full.jpg -quiet -normalize -auto-level -resize 720 cam/<?= $cam['id'] ?>_mid.jpg
  <?
  } 
  else 
  {
  ?>
  cp cam/<?= $cam['id'] ?>_full.jpg cam/<?= $cam['id'] ?>_mid.jpg &
  <?
  }
  ?>
  cp cam/<?= $cam['id'] ?>_full.jpg timelapse/<?= $cam['id'] ?>/$date_string.jpg &
  <?
}
?>
chmod 765 *

#nohup rsync -az timelapse h1:htdocs/rpgp.org/cam/ > /dev/null 2>&1 &

<?

  $script = ob_get_clean();
  print($script);
  @unlink('data/cam/getdata.sh');
  WriteToFile('data/cam/getdata.sh', $script);

?>