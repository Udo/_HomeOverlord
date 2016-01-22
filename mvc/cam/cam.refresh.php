<?
  shell_exec('nohup /bin/sh data/cam/getdata.sh &');
?>
<h3>Manual Cam Refresh</h3>
Please stand by...
<script>
  
  setTimeout(function() {
    document.location.href='<?= actionUrl('index', 'cam') ?>';
    }, 1000);
  
</script>