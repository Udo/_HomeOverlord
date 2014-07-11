<?php
header('content-type: text/plain');
ob_start();

$camConfig = cfg('cameras');
/*$nv->get('cameras');
if(!$camConfig['cams']) $camConfig['cams'] = array(
  array('photoUrl' => 'http://10.32.4.109:8080/photo.jpg', 'id' => 'cam01'),
  );*/

@mkdir('data/cam/', 0775, true);

?>
cd "<?= $GLOBALS['APP.BASEDIR'].'/data/cam/' ?>"

# polling camera photos

<?
foreach($camConfig['cams'] as $cam)
{
?>
curl -s -m 7 --retry 1 <?= $cam['photoUrl'] ?> > <?= $cam['id'] ?>_full.jpg & 
<?
}
?>

# wait until all data should be saved
sleep 15

# remove empty files
# find ./ -size 0 -print0 |xargs -0 rm

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
convert <?= $cam['id'] ?>_full.jpg -quiet -normalize -auto-level -resize 720 <?= $cam['id'] ?>_mid.jpg
<?
} 
else 
{
?>
cp <?= $cam['id'] ?>_full.jpg <?= $cam['id'] ?>_mid.jpg
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