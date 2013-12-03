//XMLRPC
var homegearAddress = '127.0.0.1';
var homegearPort = 2001;
var xmlrpcServerAddress = '127.0.0.1';
var xmlrpcServerPort = 9091;
var xmlrpc = require('xmlrpc');
var xmlrpcServer = xmlrpc.createServer({ host: '0.0.0.0', port: xmlrpcServerPort });
var xmlrpcClient = xmlrpc.createClient({ host: homegearAddress, port: homegearPort, path: '/'});
 
/*
HM Supported Parameters:

PRESS_LONG, PRESS_LONG_RELEASE, TEMPERATURE, PRESS_SHORT, HUMIDITY, LEVEL, STATE, BRIGHTNESS, MOTION, SETPOINT, VALVE_STATE, STOP, WORKING, INSTALL_TEST, PRESS_CONT, ERROR, UNREACH, LOWBAT, MODE_TEMPERATUR_VALVE
*/
 
coolDownTimers = {};
 
var registeredDevices = [];
 
/***************************************/
/*************** XMLRPC ****************/
/***************************************/
xmlrpcServer.on('NotFound', function(method, params) {
  console.log('Method ' + method + ' does not exist');
})
 
xmlrpcServer.on('system.listMethods', function (err, params, callback) {
        console.log('Method called: \'system.listMethods\'')
        callback(null, ['system.listMethods', 'system.multicall', 'event'])
});
 
xmlrpcServer.on('system.multicall', function (err, params, callback) {
        if(params instanceof Array && params[0] instanceof Array) {
                for(var i = 0; i < params[0].length; i++) {
                        if(!params[0][i].params || params[0][i].params.length != 4) continue;
                        //if(params[0][i].methodName == 'event') {
                        var ct = JSON.stringify(params[0][i]);
                        if(!coolDownTimers[ct])
                        {
                          console.log(ct);
                          coolDownTimers[ct] = true;
                          setTimeout(function(){ coolDownTimers[ct] = false; }, 500);
                        }                          
                        //}
                }
        }
        callback(null, null)
});
 
setTimeout(function () {
        xmlrpcClient.methodCall('init', ['http://' + xmlrpcServerAddress + ':' + xmlrpcServerPort, 'HomegearClient'], function (error, value) {})
}, 1000);
 
