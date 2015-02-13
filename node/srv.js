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
var xmlrpcServer = false;
var xmlrpcClient = false;

var serverState = {
  deviceState : {},
  tickLog : {},
  coolDownTimers : {},
  eventQueue : [],
  namedTimers : {},
}

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

parseJSON = function(raw, defaultValue) {
  try {
    return(JSON.parse(raw));
  } catch(err) {
    return(defaultValue);
  }
}

// add now command to queue
queueEvent = function(ev, times) {
  
  if(!times) times = 1;
  var eventQueueIsEmpty = serverState.eventQueue.length == 0;
  
  if(ev.id > 0) {
    for(var tc = 0; tc < times; tc++)
      serverState.eventQueue.push(ev);
    // restart timer 
    if(eventQueueIsEmpty) {
      queueWorkStep();
    }
  }
}

queueWorkStep = function() {

  if(serverState.eventQueue.length > 0 && child == null) {
    
    var item = serverState.eventQueue.splice(0, 1)[0];
    
    // if item.value isn't defined, use the global device state
    if(serverState.deviceState['d'+item.id]) 
      item.value = serverState.deviceState['d'+item.id].state;
    
    var cmdStr = __dirname+'/he853 '+(item.id)+' '+(item.value);
    console.log('> command: '+cmdStr, JSON.stringify(item));
    child = exec(cmdStr, execHandler);
      
  }

  if(serverState.eventQueue.length > 0) {
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

incomingDeviceEvent = function(bus, id, param, value, key, stxt) {
  var eventData = {
    type : bus,
    device : id,
    param : param,
    value : value,
    key : key,
    stxt : stxt,
    };
  cmdHttpPost({ controller : 'svc', action : 'ajax_event', data : JSON.stringify(eventData)});
  if(wss) 
    wss.broadcast({ type : 'busmessage', data : eventData });
}


/****************************************************************************************/
// ======================== Command Interface Server ====================================
// listens on runtimeConfig.commandInterfaceServerPort for commands
/****************************************************************************************/

var commandInterfaceServer = {

  setup : function() {
    return(this);
  },
  
  commands : {
    
    info : function(query, res) {
      if(query.about)
        res.write(JSON.stringify(serverState[query.about]));
      else
        res.write(JSON.stringify(serverState));
    },
    timer : function(query, res) {
      if(query.countDown == 0)
        delete serverState.namedTimers[query.name];
      else
        serverState.namedTimers[query.name] = query;
    },
    fire : function(query) {
      incomingDeviceEvent(query.bus, query.device, query.param, query.value, query.key, query.stxt);
    },
    broadcast : function(query) {
      if(wss)
        wss.broadcast(query);
    },
    busmessage : function(query) {
      var msg = parseJSON(query.data);
      if(query.fireevent == 'Y')
        commandInterfaceServer.commands.fire(msg);
      else if(wss)
        wss.broadcast({
          type : 'busmessage',
          data : msg,
        });
    },
    gpio : function(query, res) {
      if(runtimeConfig.enableGPIO)
        gpioInterface.impulse(query.param, query.value);
    },
    update : function(query, res) {
      query.type = 'devicestatus';      
    
      incomingDeviceEvent(query.bus, query.id, query.param, query.value, query.key, query.stxt);
      
      if(query.bus == 'HE') {
        if(serverState.deviceState['d'+query.id])
          serverState.deviceState['d'+query.id].state = query.value;
        queueEvent({ id : query.id, value : query.value }, 3);
      }
      else if(query.bus == 'HM') {
        if(query.value == 'true') query.value = true;
        if(query.value == 'false') query.value = false;
        if(serverState.deviceState['d'+query.id])
          serverState.deviceState['d'+query.id].state = query.value == true ? 1 : 0;
        xmlrpcClient.methodCall(
          'setValue', 
          [ query.id, query.param, query.value ], function (error, value) {});           
      }
    },
    hmcall : function(query, res) {
      xmlrpcClient.methodCall(
        query.method, 
        query.params.split(','), function (error, value) {});
    },
    
  },

  start : function() {

    http.createServer(function (req, res) {
    
      var params = require('url').parse(req.url, true);
    
      res.writeHead(200, {'Content-Type': 'text/plain'});
  
      if(commandInterfaceServer.commands[params.query.cmd]) 
        commandInterfaceServer.commands[params.query.cmd](params.query, res);
  
      res.end();

      console.log('> commandInterfaceServer.cmd(%s)', util.inspect(params.query).replace(/\n/g, ''));
        
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
        console.log('> clientsUpdateServer.connect(%s, %s) ', 
          ws.remoteAddress, 
          ws.upgradeReq.headers['x-forwarded-for']);
    
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
      //console.log('- clientsUpdateServer.broadcast('+data.type+')');
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
          console.log('< serverTickCron.tick('+runtimeConfig.httpServerUrl+'?'+reqParams+')',
            Math.round(body.length/1024)+'kB ',
            body.substr(0, 32).replace(/\n/g, ''));
          serverState.tickLog[ctr+'/'+act] = Math.floor(new Date() / 1000);
          if(doneFunc) doneFunc(body);
        } catch(err) {}
      });
    }).on('error', function(e) {
        console.log("! Error, could not execute "+act+" command: ", e);
    });
  },
  
  tickCron : function() {
    serverTickCron.tick('svc', 'ajax_tick', function(data) {
      console.log('> server tick', (data || '').substr(0, 128));
      });
    setTimeout(serverTickCron.tickCron, OneMinute);
  },
  
  tickWeather : function() {
    serverTickCron.tick('svc', 'weather', function() {
      console.log('> sync remote weather data');
    });
    setTimeout(serverTickCron.tickWeather, 10*OneMinute);
  },
  
  tickDeviceStates : function() {
    serverTickCron.tick('svc', 'ajax_getstate', function(data) {
      console.log('> reload device states from DB', (data || '').data.length);
      serverState.deviceState = parseJSON(data, serverState.deviceState);
      });
    setTimeout(serverTickCron.tickDeviceStates, OneMinute*10);
  },
  
  tickClientReload : function() {
    wss.broadcast({ type : 'reload' });
    setTimeout(serverTickCron.tickClientReload, OneMinute*60);
  },
  
  tickCams : function() {
    //console.log('- cam poll ../data/cam/getdata.sh');
    if(wss) wss.broadcast({ type : 'camtick' });
    exec('/bin/sh ../data/cam/getdata.sh', function(){});
    setTimeout(serverTickCron.tickCams, OneMinute*0.5);
  },
  
  tickHeartbeat : function() {
    Object.keys(serverState.namedTimers).forEach(function(key) {
        var tmr = serverState.namedTimers[key];
        tmr.countDown -= 1;
        if(tmr.countDown <= 0) {
          console.log('- timer ended: '+key);
          cmdHttpPost({ controller : 'svc', action : 'ajax_timer', data : JSON.stringify(tmr)});
          wss.broadcast({ type : 'timerState', deviceKey : tmr.key, countDown : 0 })
          delete serverState.namedTimers[key];
        } else if(tmr.countDown % 5 == 0) {
          console.log('- timer active... '+key+' '+tmr.countDown+'sec');
          wss.broadcast({ type : 'timerState', deviceKey : tmr.key, countDown : tmr.countDown })
        }
      });
    setTimeout(serverTickCron.tickHeartbeat, 1000);
  },
  
  setup : function() {
    return(this);
  },

  start : function() {
    serverTickCron.tickDeviceStates();
    setTimeout(serverTickCron.tickCron, OneMinute);   
    setTimeout(serverTickCron.tickWeather, OneMinute*0.5);   
    setTimeout(serverTickCron.tickClientReload, OneMinute*60);   
    setTimeout(serverTickCron.tickCams, 10000);   
    setTimeout(serverTickCron.tickHeartbeat, 1000);
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
            if(!serverState.coolDownTimers[ct]) {
              incomingDeviceEvent('HM', pr[1], pr[2], pr[3]);
              console.log('> homeMaticInterface.receive('+util.inspect(params[0][i]).replace(/\n/g, '')+')');
              // to do: memory leak
              serverState.coolDownTimers[ct] = true;
              setTimeout(function(){ serverState.coolDownTimers[ct] = false; }, 500);
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

setTimeout(function() { 
  serverTickCron.setup().start();
  homeMaticInterface.setup().start();
  clientsUpdateServer.setup().start();
  commandInterfaceServer.setup().start();
  if(runtimeConfig.enableGPIO) 
    gpioInterface.setup().start();
}, 10000);

console.log('- spooling up server in 10s');

setInterval(function() {
  var memStats = process.memoryUsage();
  console.log(
    '- mem stats '+util.inspect(memStats).replace(/\n/g, '')+' // '+Math.round(memStats.heapUsed/(1024*1024))+' MB heap size');
}, 1000*60*5);