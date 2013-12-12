HomeOverlord
===========

HomeOverlord is a simple web-based home automation interface for HomeEasy (HE853) and HomeMatic (CUL/Homegear)

![](https://raw.github.com/Udo/HomeOverlord/master/doc/hc-ui-1.png)

This is (for the time being) the main screen of HomeOverlord, the panel where you can control devices directly. The UI switches automatically between a day and night color scheme. Beyond that, HomeOverlord provides a neat system of event triggers to make your little device minions do whatever you want, behind the scenes. 

# Beware!

At this stage, this is a project that runs for me, but it's not really designed to be portable to other homes. In theory, it might work. It might not. The software is designed to work with the HomeEasy HE853 USB stick to address HomeEasy devices, and the CUL using the Homegear XMLRPC interface to communicate with HomeMatic devices. To be a full home automation solution, features are still missing. For example, right now you have to do HomeMatic pairing with the (albeit browser-based) command line interface. I run the software on a Raspberry Pi, in theory it should work well on pretty much any architecture that supports those USB devices. For reference, I included my current home configuration verbatim. Also, there is no installer.

![](https://raw.github.com/Udo/HomeOverlord/master/doc/hc-ui-3.png)

# Requirements

Installing this thing is painful. You're going to need the following:

* Hardware: a spare Linux-ready device, preferrably a Raspberry Pi or something similar. For HomeEasy devices, there is a simple HomeEasy USB sender you can buy, plug it in. For HomeMatic devices, use a CUL or really anything that works with Homegear. I'm using both since I have both systems deployed at home. Alternatively to the CUL/Homegear combo, you could probably use the HomeMatic CCU1/2 via its XMLRPC interface. Since HomeOverlord sends HM XMLRPC to port 2001 and listens at 9091 on the localhost, you will have to do some port forwarding to the actual CCU. Alternatively, you could edit the source code of node/srv.js to the CCU address. Since I don't have a CCU, I have no idea if this works but it should.

* A LAMP-style web server supporting PHP and preferrably with websocket forwarding. I use nginx/php-fpm/MySQL. Clone the repository into your web server's htdocs directory. Create a database named hc or similar, import the SQL file  from setup/ and edit the file config/defaults.php with your database credentials. 

* For HomeMatic support, install https://github.com/hfedcba/Homegear (or download the pre-compiled version). 

* Node.js and at least the following npm modules: http, sys, request, child_process, url, ws, xmlrpc, and possibly others. Start the node process manually ("node node/srv.js") to check for error messages. If that works out, you can start the process as a daemon with node/start.sh - this will also start the homegear daemon if present.

* For HomeEasy support, plug in the HomeEasy USB stick. I have checked in a version of the driver that runs on the Raspberry Pi. If you're not on Pi, you're going to have to recompile the driver in bin/he853-remote/

* Open phpMyAdmin or any other MySQL admin interface and create an account for yourself in the accounts table. Hash the password with sha1 (I know, just go with it).

* Open the file config/defaults.php and edit the "geo" section to reflect your longitude, latitude, city name, and time zone name.

# Things That Work

* HomeOverlord lets you organise your home automation by room.
* Control any kind of HomeEasy and HomeMatic switch.
* Control HomeMatic window blinds actuators.
* Hook up HomeMatic sensors to switches and other actuators (even HomeEasy)
* Create a home automation schedule based on events such as sensor input, time of day, sunset and sunrise, and weather.

# Things That Don't Work Right Now

* Dimmers
* Homematic pairing straight from the UI
* Creating and editing devices from the UI (you'll have to do that in phpMyAdmin)
* Creating and editing event handlers (also, phpMyAdmin)
* Devices acting in groups
* Flexible settings for blind actuators
* Sound output
* Granular admin functions
* Hierarchical reasonic logic. Right now device status is handled by simple IF-THEN-style logic: an event triggers a reaction. But that means HomeOverlord can't reason about event priorities. For example, if it's cloudy outside, I'd like HomeOverlord to turn on certain lights (that part works right now), but if I turn them off manually I want it to know that they should stay off. So in that example manual control would have to have a higher priority than the weather-based trigger. This is actually a nice feature that I would like to explore further.

# How to add a HomeEasy device

* Use phpMyAdmin to create a device in the devices table. Set at least the following variables: d_bus="HE", d_type to device type such as "Light", "d_name" to a descriptive name, d_id to the HomeEasy device id you want it to have, for example "1001", d_room to the room it's in.
* Activate pairing mode on the device
* Bring up the HomeOverlord devices list and switch on the device
* The device should now be paired

# How to add a HomeMatic device

* In HomeMatic go to the Devices ] Command Line screen, and enter "hm.setInstallMode true", hit return. Homegear should now give you 60 seconds to pair.
* Activate pairing mode on the device. If the device requires pairing to be confirmed, do that again.
* After pairing, follow the link on the Command Line screen to create a dataset for the device.
* Use phpMyAdmin to fill in the missing details about the device.

![](https://raw.github.com/Udo/HomeOverlord/master/doc/hc-ui-2.png)

# Event Handlers

Right now, there is no UI for event handlers, so you'll have to open phpMyAdmin and use that. All automation logic is in the events table. Open it. The events table has the following fields:

## The "events" Table

* e_key: an automatically generated event handler key
* e_type: either "C" for externally triggered events such as a HomeMatic key press event, or "T" for timer-based events that are triggered internally
* e_address: the address of the event. This has a special syntax (more about that below).
* e_address_rev: the address to reverse the event, this is optional.
* e_code: the actual event handler code (more about that below).
* e_lastcalled, e_order, e_cooldown: the system fills these columns, you can ignore them for now.

## Device-Triggered Event Addresses

The following event address codes are supported right now for "C" type events. These events are triggered from a specific device [id], on a bus [bus] ("HM" for HomeMatic), regarding a parameter [param], and a [value]. 

* "ALL": this address reacts to all device events
* "[bus]": activated by all events on that bus, for example "HM" for all HomeMatic events
* "[bus]-[id]": activated by all events from that device
* "[bus]-[param]": activated by all events regarding that parameter
* "[bus]-[device]-[param]": activated by all events from that device regarding that parameter
* "[bus]-[device]-[param]-[value]": activated by all events from that device regarding that parameter having that value
* "[bus]-[device]-[day|night]-[param]": activation on either "day" or "night", as defined by sunset and sunrise of that day
* "[bus]-[device]-[day|night]-[param]-[value]": selective activation at day or night time

For example, an HomeMatic key device called "KEQ0180768:1" issues a "PRESSED" parameter when it's activated. To catch that, make an event handler with the address "HM-KEQ0180768:1-PRESSED".

## Timer-Triggered Event Addresses

The following list described system-driven address codes for "T" type events.

* "TICK": called every timer tick (usually once per minute)
* "TIME-[HH]:[MM]" where [HH] is the hour and [MM] is the minute of the current time
* "[DOW]-[HH]:[MM]" where [DOW] is either the day of the week "MONDAY", "TUESDAY", "WEDNESDAY", "THURSDAY", "FRIDAY", "SATURDAY", or "SUNDAY"
* "[WD]-[HH]:[MM]" where [WD] is either "WEEKDAY" or "WEEKEND"
* "SUNRISE" or "SUNSET" for the moment of sunrise, sunset
* "SUNRISE+[OFFSET]" or "SUNSET+[OFFSET]" or "SUNRISE-[OFFSET]" or "SUNSET-[OFFSET]" where [OFFSET] is the offset in minutes to either sunrise or sunset
* "DAY-[WEATHER]" where [WEATHER] is an openweathermap.org weather name, such as "CLOUDS"
* "DAY-DARK" when weather data indicates heavy clouds

## Event Handler Shortcodes

In the "e_code" field of the events table, you can use short device command codes to control your devices. Use one line per device. The general format is:

> :[DeviceID]:[PARAM]:[value]

For example, if you want to activate a light called "HallwayLight":

> :HallwayLight:STATE:1

If you also used a reverse event address, you can give a second value to take care of that event:

> :[DeviceID]:[PARAM]:[value1]:[value2]

For example, you have two buttons BTN1 and BTN2, and want to use them to switch a light called "HallwayLight" on and off:

```
e_address = "BTN1-PRESSED" 
e_address_rev = "BTN2-PRESSED" 
e_code = ":HallwayLight:STATE:1:0" 
```

## Event Handler Code

Instead of device commands, you can also use PHP code in the event handler.























