<?
  
  $GLOBALS['pagetype'] = 'html';
  
?><h1>
  <div style="float:right;"><a onclick="document.location.reload();" class="fa fa-refresh"></a></div>
  ZWAVE STATUS
</h1>

<style>
  
.panels > div {
  padding: 8px;
  background: rgba(255,255,255,0.5);
  display: inline-block;
  width: 100%;
  min-height: 350px;
  margin-right: 8px;
  margin-bottom: 8px;
  border: 1px solid rgba(0,0,0,0.1);
}

.device {
  display: inline-block;
  width: 20%;
  min-width: 180px;
  xheight: 150px;
  border: 1px solid rgba(0,0,0,0.1);
  margin-right: 8px;
  margin-bottom: 8px;
  overflow: hidden;
  vertical-align: top;
  xtransition: all 0.5s linear;
}

.device-header {
  padding: 6px;
  padding-bottom: 6px;
  background: rgba(0,0,0,0.2);
  white-space: nowrap;
}

.device-info {
  padding: 6px;
  padding-top: 4px;
  font-size: 80%;
  background: rgba(0,0,0,0.1);
}

.blinking-icon {
  color: red;
  animation: red-color-pulse 0.5s infinite;
  display: inline-block;
}

.heat-alarm {
  background: rgba(255,255,0,0.8);
  color: black;
}

.motion-alarm {
  color: black;
  animation: blue-pulse 1s infinite;
}

.smoke-alarm {
  background: rgba(255,0,0,0.8);
  color: white;
  animation: red-pulse 1s infinite;
}

@keyframes red-pulse {
  0% {
    background-color: rgba(255,0,0,1.0);
  }
  50% {
    background-color: rgba(255,0,0,0.5);
  }
  100% {
    background-color: rgba(255,0,0,1.0);
  }
}
  
@keyframes blue-pulse {
  0% {
    background-color: rgba(80,180,255,1.0);
  }
  50% {
    background-color: rgba(80,180,255,0.5);
  }
  100% {
    background-color: rgba(80,180,255,1.0);
  }
}
  
@keyframes red-color-pulse {
  0% {
    color: rgba(255,0,0,1.0);
  }
  50% {
    color: rgba(0,0,0,1.0);
  }
  100% {
    color: rgba(255,0,0,1.0);
  }
}
  
</style>

<script src="js/masonry.js"></script>
<div class="panels" id="panel-root">
<? 
  $isEmbedded = true;
  include('mvc/zwave/zwave.index-fragment.php'); 
?>
</div>

<script>
  $('.panels').masonry({

  });
  setTimeout(function() {
    document.location.reload();
  }, 1000000);
  setInterval(function() {
    $.post('/hc/?/zwave/index-fragment', 
      {}, function(data) {
      $('#panel-root').html(data);
    })
  }, 10000);
</script>