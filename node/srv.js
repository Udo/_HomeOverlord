http = require('http');
sys = require('sys');
request = require('request');
exec = require('child_process').exec;
child = null;

deviceState = {};

if (!String.prototype.trim) {
  String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, '');
  };
}

// called after command line was invoked
execHandler = function (error, stdout, stderr) { 
  //console.log('> '+stdout.trim());
  child = null;
}

eventQueue = [];

// add now command to queue
queueEvent = function(ev) {
  if(ev.id > 0) {
    eventQueue.push(ev);
    //console.log('queue length='+eventQueue.length);
  }
}

// ticker function which executes the queue
setInterval(function() {

  if(eventQueue.length > 0 && child == null) {
    
    var item = eventQueue.splice(0, 1)[0];
    
    // if item.value isn't defined, use the global device state
    if(!item.value && deviceState['d'+item.id]) item.value = deviceState['d'+item.id].state;
    
    //console.log('queue item id='+item.id+' value='+item.value+' queue length='+eventQueue.length);
    child = exec('sh /srv/www/htdocs/hc/bin/switch.sh '+(0+item.id)+' '+(0+item.value), 
      execHandler);
  }

}, 1000);

cmdHttpPost = function(params) {

  request.post(
    'http://localhost/hc/',
    { form: params },
    function (error, response, body) {
      
    }
  );

}

/****************************************************************************************/
// ======================== Command Interface Server
/****************************************************************************************/

http.createServer(function (req, res) {

  var params = require('url').parse(req.url, true);

  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end();

  if(params.query.cmd == 'broadcast') {
  
    if(wss)
      wss.broadcast(params.query);
  
  }
  else if(params.query.cmd == 'timedevent') {
  
    if(!params.query.minutes || params.query.minutes <= 0)
      params.query.minutes = 60;
      
    setTimeout(function() {
      cmdHttpPost({ 
        controller : 'svc', 
        action : 'ajax_notify', 
        data : JSON.stringify(params.query)});
      }, params.query.minutes*1000*60);
  
  }
  else if(params.query.cmd == 'update') {
    
    params.query.type = 'devicestatus';
    
    res.end(params.query.id+'='+params.query.value+'\n\n');
  
    // notify clients about state change
    if(wss)
      wss.broadcast(params.query);
    
    console.log('> '+JSON.stringify(params.query));
  
    if(params.query.bus == 'HE') {
      // update device state table for HomeEasy
      if(deviceState['d'+params.query.id])
        deviceState['d'+params.query.id].state = params.query.value;
  
      queueEvent({ id : params.query.id });
      queueEvent({ id : params.query.id });
      queueEvent({ id : params.query.id });
    }
    else if(params.query.bus == 'HM') {
      if(params.query.value == 'true') params.query.value = true;
      if(params.query.value == 'false') params.query.value = false;
      if(deviceState['d'+params.query.id])
        deviceState['d'+params.query.id].state = params.query.value == true ? 1 : 0;
      xmlrpcClient.methodCall(
        'setValue', 
        [ params.query.id, params.query.param, params.query.value ], function (error, value) {});           
    }
  } 
  else if(params.query.cmd == 'hmcall') {
    xmlrpcClient.methodCall(
      params.query.method, 
      params.query.params.split(','), function (error, value) {});           
  }  
  
}).listen(1080);

console.log('http server started');

/****************************************************************************************/
// ======================== Realtime Client Update Server
/****************************************************************************************/

var WebSocketServer = require('/usr/local/lib/node_modules/ws').Server;

wss = new WebSocketServer({port: 1081});

wss.on('connection', function(ws) {

    ws.remoteAddress = ws._socket.remoteAddress;
    console.log('client connected '+JSON.stringify(ws.remoteAddress));

    ws.on('close', function(sock) {
      console.log('client disconnected '+JSON.stringify(ws.remoteAddress));
    });
    
    ws.on('error', function(sock) {
      console.log('error '+JSON.stringify(sock));
    });
    
    ws.on('message', function(message) {
        console.log('received: %s', JSON.stringify(message));
    });

});

wss.broadcast = function(data) {
  var sdata = JSON.stringify(data);
  for(var i in this.clients)
    this.clients[i].send(sdata);
};

console.log('websocket server started');

/****************************************************************************************/
// ======================== Device State Cache
/****************************************************************************************/

// get current device states
getDeviceStatesFromDB = function() {
    http.get('http://localhost/hc/?action=ajax_getstate&controller=devices', function(res) {
      var body = '';
  
      res.on('data', function(chunk) {
          body += chunk;
      });
  
      res.on('end', function() {
        try {
          deviceState = JSON.parse(body);
        } catch(err) {
        
        }
      });
    }).on('error', function(e) {
        console.log("Error, could not retrieve device states: ", e);
    });
  };

getDeviceStatesFromDB();

setInterval(function() {

  // periodically update all clients with device states
  //wss.broadcast({ type : 'globalstate', data : deviceState });
  wss.broadcast({ type : 'reload' });

  }, 1000*60*60);
  
/****************************************************************************************/
// ======================== SERVER TIME TICK
/****************************************************************************************/

makeServerTick = function(ctr, act) {
    getDeviceStatesFromDB();
    http.get('http://localhost/hc/?action='+act+'&controller='+ctr, function(res) {
      res.on('end', function() {});
    }).on('error', function(e) {
        console.log("Error, could not execute "+act+" command: ", e);
    });
  };
  
setInterval(function() {
  makeServerTick('svc', 'ajax_tick');
  }, 1000*60);

setInterval(function() {
  makeServerTick('svc', 'weather');
  }, 1000*60*10);  

/****************************************************************************************/
// ======================== XML RPC Connection to Homematic Control
/****************************************************************************************/

//XMLRPC
var homegearAddress = '127.0.0.1';
var homegearPort = 2001;
var xmlrpcServerAddress = '127.0.0.1';
var xmlrpcServerPort = 9091;
var xmlrpc = require('xmlrpc');
var xmlrpcServer = xmlrpc.createServer({ host: '0.0.0.0', port: xmlrpcServerPort });
xmlrpcClient = xmlrpc.createClient({ host: homegearAddress, port: homegearPort, path: '/'});
 
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
        //console.log('HM XMLRPC Method called: \'system.listMethods\'');
        console.log('HM XMLRPC Connector online');
        callback(null, ['system.listMethods', 'system.multicall', 'event']);
});
 
xmlrpcServer.on('system.multicall', function (err, params, callback) {
        if(params instanceof Array && params[0] instanceof Array) {
                for(var i = 0; i < params[0].length; i++) {
                        if(!params[0][i].params || params[0][i].params.length != 4) continue;
                        //if(params[0][i].methodName == 'event') {
                        var ct = JSON.stringify(params[0][i]);
                        var pr = params[0][i].params;
                        if(!coolDownTimers[ct])
                        {
                          var eventData = {
                            type : 'HM',
                            device : pr[1],
                            param : pr[2],
                            value : pr[3]
                            };
                          cmdHttpPost({ controller : 'svc', action : 'ajax_event', data : JSON.stringify(eventData)});
                          if(wss) 
                            wss.broadcast({ type : 'busmessage', data : eventData });
                          console.log('HM RT CMD -- '+ct);
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
 
setInterval(function () {
        xmlrpcClient.methodCall('init', ['http://' + xmlrpcServerAddress + ':' + xmlrpcServerPort, 'HomegearClient'], function (error, value) {})
}, 1000*60*10);
 
