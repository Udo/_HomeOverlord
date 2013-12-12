<?= $this->_getSubmenu2() ?>
<input type="text" id="cmd" value="" placeholder="prefix HomeMatic XMLRPC methods with 'hm.'" onkeypress="if(event.keyCode == 13) sendCli();"/>
<pre id="result" style="font-size: 70%;"></pre>

<script>

sendCli = function() {

  $('#result').text('processing...');

  $.post('./?', { controller : 'devices', action : 'ajax_cli', q : $('#cmd').val() }, function(data) {
  
    $('#result').html(data);
  
    });

}

</script>
<style>

#cmd {
  width: 80%;
}

</style>
<hr/>
<pre style="font-size: 70%;"><a target="_blank" href="https://www.homegear.eu/index.php/XML_RPC_Method_Reference">Methods</a>:

activateLinkParamset
addDevice
addLink
changekey
clearConfigCache
deleteDevice
determineParameter
getDeviceDescription  address
getInstallMode
getKeyMismatchDevice
getLinkInfo
getLinkPeers
getLinks
getParamset address pskey
getParamsetDescription  address pstype
getParamsetId address type
getValue address key
init  url interface
listDevices
listTeams
logLevel
putParamset address key pset
removeLink
reportValueUsage
restoreConfigToDevice
rssiInfo
searchDevices
setInstallMode
setLinkInfo
setTeam
setTempKey
setValue  address key value
system.listMethods
system.methodHelp 
system.multicall 
updateFirmware
listBidcosInterfaces
setBidcosInterface
getServiceMessages
getMetadata
setMetadata
getAllMetadata

</pre>