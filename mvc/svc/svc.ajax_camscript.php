<?php
header('content-type: text/plain');
ob_start();

$camConfig = cfg('cameras');
$sensorConfig = cfg('sensors');
/*$nv->get('cameras');
if(!$camConfig['cams']) $camConfig['cams'] = array(
  array('photoUrl' => 'http://10.32.4.109:8080/photo.jpg', 'id' => 'cam01'),
  );*/

@mkdir('data/cam/', 0775, true);
@mkdir('data/sensors/', 0775, true);

?>
cd "<?= $GLOBALS['APP.BASEDIR'].'/data/' ?>"

# polling camera photos

<?
foreach($camConfig['cams'] as $cam)
{
?>
curl -s -m 15 <?= $cam['photoUrl'] ?> > cam/<?= $cam['id'] ?>_full.jpg & 
<?
}
?>

# sensor data

<?
foreach($sensorConfig['sensors'] as $sen)
{
?>
curl -s -m 15 <?= $sen['jsonUrl'] ?> > sensors/<?= $sen['id'] ?>.json & 
<?
}
?>

# wait until all data should be saved
sleep 20

<? /*
function saveToArchive(){
  date_string=$(date +"%Y%m%d%H%M")
  mnth=$(date +"%Y%m%d")
  mkdir -p cam_archive/$1/
  cp cam/$1.jpg cam_archive/$1/$date_string.jpg
} */ ?>
# down-convert and enhance
<?
foreach($camConfig['cams'] as $cam) if($cam['resize'])
{
?>
convert cam/<?= $cam['id'] ?>_full.jpg -quiet -normalize -auto-level -resize 720 cam/<?= $cam['id'] ?>_mid.jpg
<?
} 
else 
{
?>
cp cam/<?= $cam['id'] ?>_full.jpg cam/<?= $cam['id'] ?>_mid.jpg
<?
}
?>
chmod 765 *

# rsync -az /Users/udo/cam/ h1:htdocs/rpgp.org/cam/

<?

  $script = ob_get_clean();
  print($script);
  @unlink('data/cam/getdata.sh');
  WriteToFile('data/cam/getdata.sh', $script);

?>