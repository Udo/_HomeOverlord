<?php
require_once("HM-XMLRPC-Client\\Client.php");
$Client = new \XMLRPC\Client("localhost", 2001);

//print_r($Client->send("getDeviceDescription", array("JEQ0304693:1")));
//print_r($Client->send("getLinkInfo", array("IEQ0540169:1", "JEQ0551288:3")));
//print_r($Client->send("getParamset", array("JEQ0098488", "MASTER")));
//print_r($Client->send("getParamsetDescription", array("JEQ0304693:1", "LINK")));
//print_r($Client->send("getDeviceDescription", array("JEQ0304693:2")));
//print_r($Client->send("getParamsetId", array("JEQ0304693:1", "KEQ0022695:1")));
//print_r($Client->send("getMetadata", array("IEQ0540169:2")));
//print_r($Client->send("putParamset", array("JEQ0739619:1", "MASTER", array("EVENT_DELAYTIME" => 0.0))));
//print_r($Client->send("getParamset", array("JEQ0304693:1", "KEQ0022695:1")));
//print_r($Client->send("getServiceMessages", array()));
//print_r($Client->send("listDevices", array()));
//print_r($Client->send("getLinkPeers", array("JEQ0098488:1")));
//print_r($Client->send("getLinks", array("", 24)));
//print_r($Client->send("getLinks", array()));
//print_r($Client->send("system.methodHelp", array("getLinks")));
//print_r($Client->send("setValue", array("KEQ0022695:1", "ON_TIME", 787487.3)));
//print_r($Client->send("setTeam", array("JEQ0351483:1")));
//print_r($Client->send("setTeam", array("JEQ0351483:1", "*JEQ0175087:1")));
//print_r($Client->send("setValue", array("GEQ0173382:2", "STATE", false)));
//print_r($Client->send("setValue", array("JEQ0554309:1", "VALVE_STATE", 0)));
//print_r($Client->send("setValue", array("JEQ0551288:2", "SETPOINT", 16.0)));
//print_r($Client->send("setValue", array("JEQ0304693:18", "TEXT", "Hi")));
//print_r($Client->send("setValue", array("JEQ0304693:18", "SUBMIT", true)));
//print_r($Client->send("putParamset", array("JEQ0304693:18", "MASTER", array("MESSAGE_SHOW_TIME" => 0.0))));
?>
