console.log('- loading <http>');
var http = require('http');
console.log('- loading <sys>');
var sys = require('sys');
console.log('- loading <request>');
var request = require('request');
console.log('- loading <child_process>');
var exec = require('child_process').exec;
console.log('- loading <xmlrpc>');
var xmlrpc = require('xmlrpc');
console.log('- loading <ws>');
var WebSocketServer = require('ws').Server;
console.log('- loading <util>');
var util = require('util')

var runtimeConfig = {
  clientsUpdateServerPort : 1081,
  httpServerUrl : 'http://localhost/hc/',
  homegearAddress : '127.0.0.1',
  homegearPort : 2001,
  xmlrpcServerAddress : '127.0.0.1',
  xmlrpcServerPort : 9091,
  commandInterfaceServerPort : 1080,
  enableGPIO : true,
  };

if(runtimeConfig.enableGPIO) {
  console.log('- loading <rpi-gpio>');
  var gpio = require('rpi-gpio');
}

var OneMinute = 1000*60;
var child = null;
var coolDownTimers = {};
var registeredDevices = [];
deviceState = {};
var xmlrpcServer = false;
var xmlrpcClient = false;

var memStats = process.memoryUsage();
console.log('- mem stats '+util.inspect(memStats).replace(/\n/g, '')+' // '+Math.round(memStats.heapUsed/(1024*1024))+' MB heap size');

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
queueEvent = function(ev, times) {
  
  if(!times) times = 1;
  var eventQueueIsEmpty = eventQueue.length == 0;
  
  if(ev.id > 0) {
    for(var tc = 0; tc < times; tc++)
      eventQueue.push(ev);
    // restart timer 
    if(eventQueueIsEmpty) {
      queueWorkStep();
    }
  }
}

queueWorkStep = function() {

  if(eventQueue.length > 0 && child == null) {
    
    var item = eventQueue.splice(0, 1)[0];
    
    // if item.value isn't defined, use the global device state
    if(!item.value && deviceState['d'+item.id]) item.value = deviceState['d'+item.id].state;
    
    child = exec('sh /srv/www/htdocs/hc/bin/switch.sh '+(0+item.id)+' '+(0+item.value), 
      execHandler);
      
  }

  if(eventQueue.length > 0) {
    setTimeout(queueWorkStep, 1000);
  }

};

cmdHttpPost = function(params) {

  request.post(
    'http://localhost/hc/',
    { form: params },
    function (error, response, body) {
      
    }
  );

}

/****************************************************************************************/
// ======================== Command Interface Server ====================================
// listens on runtimeConfig.commandInterfaceServerPort for commands
/****************************************************************************************/

var commandInterfaceServer = {

  setup : function() {
    return(this);
  },

  start : function() {

    http.createServer(function (req, res) {
    
      var params = require('url').parse(req.url, true);
    
      res.writeHead(200, {'Content-Type': 'text/plain'});
      res.end();

      console.log('> commandInterfaceServer.cmd(%s)', util.inspect(params.query).replace(/\n/g, ''));
    
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
          }, params.query.minutes*OneMinute);
      
      }
      else if(params.query.cmd == 'gpio' && runtimeConfig.enableGPIO) {

        gpioInterface.impulse(params.query.param, params.query.value);
      
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
      
          queueEvent({ id : params.query.id }, 3);
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
      
    }).listen(runtimeConfig.commandInterfaceServerPort);
    
    console.log('- commandInterfaceServer.start() on port '+runtimeConfig.commandInterfaceServerPort);
    },

  };

/****************************************************************************************/
// ======================== Realtime Client Update Server ===============================
// websocket server that updates clients in realtime
/****************************************************************************************/

wss = false;
var clientsUpdateServer = {

  setup : function() {
    return(this);
  },

  start : function() {
  
    wss = new WebSocketServer({port: runtimeConfig.clientsUpdateServerPort});
    
    wss.on('connection', function(ws) {
    
        ws.remoteAddress = ws._socket.remoteAddress;
        console.log('> clientsUpdateServer.connect(%s) ', JSON.stringify(ws.remoteAddress));
    
        ws.on('close', function(sock) {
          console.log('> clientsUpdateServer.close(%s) ', JSON.stringify(ws.remoteAddress));
        });
        
        ws.on('error', function(sock) {
          console.log('! clientsUpdateServer.error() '+JSON.stringify(sock));
        });
        
        ws.on('message', function(message) {
            console.log('> clientsUpdateServer.message(%s)', JSON.stringify(message));
        });
    
    });
  
    wss.broadcast = function(data) {
      console.log('- clientsUpdateServer.broadcast('+data.type+')');
      var sdata = JSON.stringify(data);
      for(var i in this.clients)
        wss.clients[i].send(sdata);
    };
    
    setTimeout(function() {
      wss.broadcast({ type : 'hello' });
      }, 1000);
    
    console.log('- clientsUpdateServer.start() on port '+runtimeConfig.clientsUpdateServerPort);
    },

  };

  
/****************************************************************************************/
// ======================== SERVER TIME TICK ============================================
// diverse timed tick operations
/****************************************************************************************/

var serverTickCron = {

  tick : function(ctr, act, doneFunc) {
    var reqParams = 'action='+act+'&controller='+ctr;
    http.get(runtimeConfig.httpServerUrl+'?'+reqParams, function(res) {
      var body = '';
      res.on('data', function(chunk) {
          body += chunk;
      });
      res.on('end', function() {
        try {
          console.log('> serverTickCron.tick('+ctr+'.'+act+') '+Math.round(body.length/1024)+'kB ['+body.substr(0, 32).replace(/\n/g, '')+'...]');
          if(doneFunc) doneFunc(body);
        } catch(err) {}
      });
    }).on('error', function(e) {
        console.log("Error, could not execute "+act+" command: ", e);
    });
  },
  
  tickCron : function() {
    serverTickCron.tick('svc', 'ajax_tick');
    setTimeout(serverTickCron.tickCron, OneMinute);
  },
  
  tickWeather : function() {
    serverTickCron.tick('svc', 'weather');
    setTimeout(serverTickCron.tickWeather, 10*OneMinute);
  },
  
  tickDeviceStates : function() {
    serverTickCron.tick('devices', 'ajax_getstate', function(data) {
      deviceState = JSON.parse(data);
      });
    setTimeout(serverTickCron.tickDeviceStates, OneMinute*10);
  },
  
  tickClientReload : function() {
    wss.broadcast({ type : 'reload' });
    setTimeout(serverTickCron.tickClientReload, OneMinute*60);
  },
  
  setup : function() {
    return(this);
  },

  start : function() {
    serverTickCron.tickDeviceStates();
    setTimeout(serverTickCron.tickCron, OneMinute);   
    setTimeout(serverTickCron.tickWeather, OneMinute*0.5);   
    setTimeout(serverTickCron.tickClientReload, OneMinute*60);   
    console.log('- serverTickCron.start()');
    },

  };

  
/****************************************************************************************/
// ======================== XML RPC Connection to Homematic Control =====================
// interface to the homegear XMLRPC daemon
/****************************************************************************************/

/* HM Supported Parameters:
    PRESS_LONG, PRESS_LONG_RELEASE, TEMPERATURE, PRESS_SHORT, HUMIDITY, LEVEL, STATE, BRIGHTNESS, MOTION, SETPOINT, VALVE_STATE, STOP, 
    WORKING, INSTALL_TEST, PRESS_CONT, ERROR, UNREACH, LOWBAT, MODE_TEMPERATUR_VALVE */

var homeMaticInterface = {

  setup : function() {
  
    xmlrpcServer = xmlrpc.createServer({ host: '0.0.0.0', port: runtimeConfig.xmlrpcServerPort });
    xmlrpcClient = xmlrpc.createClient({ host: runtimeConfig.homegearAddress, port: runtimeConfig.homegearPort, path: '/'});
  
    xmlrpcServer.on('NotFound', function(method, params) {
      console.log('! homeMaticInterface: Method ' + method + ' does not exist');
    });
     
    xmlrpcServer.on('system.listMethods', function (err, params, callback) {
      console.log('> homeMaticInterface: Connector online');
      callback(null, ['system.listMethods', 'system.multicall', 'event']);
    });
     
    xmlrpcServer.on('system.multicall', function (err, params, callback) {
      if(params instanceof Array && params[0] instanceof Array) {
        for(var i = 0; i < params[0].length; i++) {
          if(!params[0][i].params || params[0][i].params.length != 4) continue;
            var ct = JSON.stringify(params[0][i]);
            var pr = params[0][i].params;
            if(!coolDownTimers[ct]) {
              var eventData = {
                type : 'HM',
                device : pr[1],
                param : pr[2],
                value : pr[3]
                };
              cmdHttpPost({ controller : 'svc', action : 'ajax_event', data : JSON.stringify(eventData)});
              if(wss) 
                wss.broadcast({ type : 'busmessage', data : eventData });
              console.log('> homeMaticInterface.receive('+util.inspect(params[0][i]).replace(/\n/g, '')+')');
              coolDownTimers[ct] = true;
              setTimeout(function(){ coolDownTimers[ct] = false; }, 500);
            }                          
          }
        }
      callback(null, null);
      });
    console.log('- homeMaticInterface.setup() on port '+runtimeConfig.xmlrpcServerPort);
    return(this);
    },

  start : function() {
      xmlrpcClient.methodCall('init', ['http://' + runtimeConfig.xmlrpcServerAddress + ':' + runtimeConfig.xmlrpcServerPort, 'HomeOverlord'], function (error, value) {});
      setTimeout(this.connect, OneMinute*10);  
    },

  };

/****************************************************************************************/
// ======================== Raspberry Pi GPIO ===========================================
/****************************************************************************************/

var gpioInterface = {

  setup : function() {
    gpio.on('export', function(channel) {
      console.log('> gpioInterface.export() ' + channel);
      });
    return(this);
    },
    
  start : function() {
    console.log('- gpioInterface.start()');
    },
    
  pinConfig : {},

  impulse : function(pin, value, forTime) {
    console.log('> gpioInterface.impulse(%s)', pin);
    if(!forTime) forTime = 500;
    if(gpioInterface.pinConfig[pin] && gpioInterface.pinConfig[pin].out) {
      gpio.write(pin, value, function() {
        setTimeout(gpio.write(pin, value ? 0 : 1), forTime);
        });
    } else {
      gpio.setup(pin, gpio.DIR_OUT, function() {
        gpioInterface.pinConfig[pin] = { out : true };
        gpio.write(pin, value, function() {
          setTimeout(gpio.write(pin, value ? 0 : 1), forTime);
          });
        });     
    }
    },

  }; 


/****************************************************************************************/
// ======================== Initialize Objects ==========================================
/****************************************************************************************/


serverTickCron.setup().start();
homeMaticInterface.setup().start();
clientsUpdateServer.setup().start();
commandInterfaceServer.setup().start();
if(runtimeConfig.enableGPIO) 
  gpioInterface.setup().start();
