<?= H2Configuration::getAdminMenu() ?>
<?

$ds = db()->getDS('events', $_REQUEST['id']);

if(isset($_POST['id']))
{
  foreach(array('e_address', 'e_address_rev', 'e_code') as $f)
    $ds[$f] = $_REQUEST[$f];
  $ds['e_key'] = db()->commit('events', $ds);
}

?>

<form action="<?= actionUrl('edit', 'events') ?>" method="POST">
<table class="settingsTable">
  
  <input type="hidden" name="id" value="<?= first($ds['e_key'], '0') ?>"/>
  <tr>
    <td>Action</td>
    <td><input type="text" name="e_address" value="<?= htmlspecialchars(@first($ds['e_address'], $_REQUEST['eventaddress']))?>"/></td>
  </tr>
  <tr>
    <td>Reverse</td>
    <td><input type="text" name="e_address_rev" value="<?= htmlspecialchars($ds['e_address_rev'])?>"/></td>
  </tr>
  <tr>
    <td valign="top">Code</td>
    <td><textarea name="e_code"><?= htmlspecialchars($ds['e_code'])?></textarea>
  </tr>
  <tr>
    <td></td>
    <td><input type="submit" value="Save"/></td>
  </tr>


</table>
</form>

<div class="help-text">

<h1><a id="user-content-event-handler-commands" class="anchor" href="#event-handler-commands" aria-hidden="true"></a>Event Handler Commands</h1>

<p>In addition to shortcodes, event handlers can also carry out commands that allow for more complex operations. Commands can be chained together in the same line with the slash ("/") character.</p>

<h2><a id="user-content-day" class="anchor" href="#day" aria-hidden="true"></a>DAY?</h2>

<p>Executes the commands that are chained behind it if it's day time. For example:</p>

<pre><code>DAY?/MODE:Day Mode
</code></pre>

<h2><a id="user-content-night" class="anchor" href="#night" aria-hidden="true"></a>NIGHT?</h2>

<p>Executes the commands that are chained behind it if it's night time. For example:</p>

<pre><code>NIGHT?/MODE:Night Mode
</code></pre>

<h2><a id="user-content-action" class="anchor" href="#action" aria-hidden="true"></a>ACTION?</h2>

<p>Executes the commands that are chained behind it if the action was invoked (as opposed to its reverse action). For example:</p>

<pre><code>ACTION?/:WindowBlinds:LEVEL:1
</code></pre>

<h2><a id="user-content-rev" class="anchor" href="#rev" aria-hidden="true"></a>REV?</h2>

<p>Executes the commands that are chained behind it if the reverse action was invoked (as opposed to its normal action). For example:</p>

<pre><code>REV?/:WindowBlinds:LEVEL:0
</code></pre>

<h2><a id="user-content-modex" class="anchor" href="#modex" aria-hidden="true"></a>MODE?:X</h2>

<p>Executes the commands that are chained behind it if the given mode X is active. For example:</p>

<pre><code>MODE?:Night/:WindowBlinds:LEVEL:0
</code></pre>

<h2><a id="user-content-callx" class="anchor" href="#callx" aria-hidden="true"></a>CALL:X</h2>

<p>Triggers another event X. For example:</p>

<pre><code>CALL:Shutdown
</code></pre>

<h2><a id="user-content-selectx" class="anchor" href="#selectx" aria-hidden="true"></a>SELECT:X</h2>

<p>Selects a number of devices and calls the command that is chained behind it. For example, the following command selects all devices that are lights and then calls the SET command on them:</p>

<pre><code>SELECT:TYPE=Light/SET:STATE:0
</code></pre>

<p>There are different properties you can select devices by:</p>

<pre><code>SELECT:ALL          : select all devices
SELECT:NONE         : select none
SELECT:OTHER        : all devices that have not been used so far
SELECT:TYPE=X       : all devices of type X
SELECT:TYPE=BUS-X   : all devices of bus BUS and type X
SELECT:TYPE!=X      : all devices not of type X
SELECT:SUBTYPE=X    : all devices of sub-type X
SELECT:SUBTYPE!=X   : all devices not of sub-type X
SELECT:PRI=X        : all devices of priority X
SELECT:PRI&gt;X        : all devices of priority greater than X
SELECT:PRI&lt;X        : all devices of priority less than X
SELECT:GROUP=X      : all devices in group X
SELECT:GROUP!=X     : all devices not in group X
SELECT:MAP=V            : use variable V to do a map lookup
SELECT:X                : select device with name/id X
</code></pre>

<p>You can chain several of these property selectors together with the colon (":")
separator, just as you can chain commands together with the slash ("/") separator.</p>

<h2><a id="user-content-removex" class="anchor" href="#removex" aria-hidden="true"></a>REMOVE:X</h2>

<p>The REMOVE command works exactly the same as the SELECT command, except that it removes devices from the list instead of adding them.</p>

<h2><a id="user-content-autoxy" class="anchor" href="#autoxy" aria-hidden="true"></a>AUTO:X:Y</h2>

<p>Sets the automation property to X (or Y for the reverse action) of the selected devices. Valid values for automation are either "M" for manual, or "A" for automatic. For example, the following command switches all lights to manual, and then to automatic for the reverse action:</p>

<pre><code>SELECT:TYPE=Light/AUTO:M:A
</code></pre>

<h2><a id="user-content-setpxy" class="anchor" href="#setpxy" aria-hidden="true"></a>SET:P:X:Y</h2>

<p>Sets the property P of the selected devices to value X (or Y for the reverse action). For example, the following command turns on all lights, and switches them off for the reverse action:</p>

<pre><code>SELECT:TYPE=Light/SET:STATE:1:0
</code></pre>

<h2><a id="user-content-modex-1" class="anchor" href="#modex-1" aria-hidden="true"></a>MODE:X</h2>

<p>Activates the mode X. For example, the following command switches the house to Night mode:</p>

<pre><code>MODE:Night
</code></pre>

<h2><a id="user-content-mapx--y" class="anchor" href="#mapx--y" aria-hidden="true"></a>MAP:X &gt; Y</h2>

<p>Maps value X to value Y. This can later be used with the SELECT:MAP statement, or using 
$this-&gt;map[] in a PHP/ line.</p>

<pre><code>MAP:OfficeThermo &gt; OfficeHeater
</code></pre>

<h2><a id="user-content-log" class="anchor" href="#log" aria-hidden="true"></a>LOG</h2>

<p>Put the current state of the script into the log file log/event.debug.log. The following example selects all lights and puts the result into the log file:</p>

<pre><code>SELECT:TYPE=Light
LOG
</code></pre>

<h2><a id="user-content-logline" class="anchor" href="#logline" aria-hidden="true"></a>LOGLINE</h2>

<p>Puts information about the current line into log/event.debug.log. The following example selects all lights and puts the result into the log file:</p>

<pre><code>SELECT:TYPE=Light/LOGLINE
</code></pre>

<h2><a id="user-content-php" class="anchor" href="#php" aria-hidden="true"></a>PHP</h2>

<p>Executes the rest of the line as PHP code:</p>

<pre><code>PHP/$this-&gt;map['bar'] = 'foo'
</code></pre>

<h2><a id="user-content-thermoswitcht" class="anchor" href="#thermoswitcht" aria-hidden="true"></a>THERMOSWITCH:T</h2>

<p>This command can be used to drive a simple on/off heating device with a thermostat.</p>

<p>T must be a thermostat device that provides both the SET_TEMPERATURE and TEMPERATURE parameters. <em>If T is omitted, the event emitter is used as the thermostat instead</em>.</p>

<p>THERMOSWITCH then compares both parameter values and determines if commands down the line should use the normal action or the reverse action. If THERMOSWITCH comes to the conclusion that heating should be applied, the normal action is selected - if the heather should be turned off, the reverse action is selected.</p>

<p>The following example selects all devices of subtype "heater", uses thermostat "thermo1", and turns them on or off accordingly:</p>

<pre><code>SELECT:SUBTYPE=heater/THERMOSWITCH:thermo1/SET:STATE:1:0
</code></pre>

<p>The following example maps thermostats to heaters and switches them if the current mode is "At Home" (using this method you can use a single event handler to switch all the heaters in different rooms individually):</p>

<pre><code>MAP:OfficeThermo &gt; OfficeHeater
MAP:LivingRoomThermo &gt; LivingRoomHeater

MODE?:At Home/SELECT:MAP=emitter_alias/THERMOSWITCH/SET:STATE:1:0
</code></pre>

<h1><a id="user-content-php-scripts-as-event-handlers" class="anchor" href="#php-scripts-as-event-handlers" aria-hidden="true"></a>PHP Scripts as Event Handlers</h1>

<p>You can also use plain PHP code in your event handlers for maximum customizability. Pure PHP event scripts start with the line "#PHP".</p>

<p>From within your PHP code, you can call both Event Shortcodes or Commands by using the $command() function.</p>

<p>For example, the following code activates logging to log/event.debug.log, turns on the living room lights if the emitter value was above 10:</p>

<pre><code>#PHP
$log = true;
if(emitter_value &gt; 10) $command(':LivingRoomLights:STATE:1');
</code></pre>

<h2><a id="user-content-php-variables" class="anchor" href="#php-variables" aria-hidden="true"></a>PHP Variables</h2>

<p>The following pre-defined variables exist for your PHP scripts:</p>

<pre><code>$emitter_id: ID of the device that caused the event
$emitter_param: name of the event parameter triggered 
$emitter_value: value of the parameter
$emitter_alias: alias of the emitter device (if any)
$emitter_root: name of the root device (if any)
$emitter_name: human-friendly name of the emitter (if any)
$reverseAction: if the reverse action has been triggered
</code></pre>

<h2><a id="user-content-php-functions" class="anchor" href="#php-functions" aria-hidden="true"></a>PHP Functions</h2>

<p>Theoretically, you can call any HomeOverlord function from your PHP scripts, but then you run the chance of breaking things. Stable API functions you can call from custom event handlers include:</p>

<pre><code>broadcast($msg): broadcasts the array as a message to all clients
getExtendedDeviceState($deviceID): gets a list of all the device's paramters
getDeviceDS($deviceID): gets the device data set by its ID (or alias, or key)

</code></pre>

<h1><a id="user-content-client-settings-by-ip" class="anchor" href="#client-settings-by-ip" aria-hidden="true"></a>Client Settings by IP</h1>

<p><a href="https://camo.githubusercontent.com/6c597774cd41650a33d3771c5c65bb65f1c6d839/68747470733a2f2f7261772e6769746875622e636f6d2f55646f2f486f6d654f7665726c6f72642f6d61737465722f646f632f636c69656e74732e706e67" target="_blank"><img src="https://camo.githubusercontent.com/6c597774cd41650a33d3771c5c65bb65f1c6d839/68747470733a2f2f7261772e6769746875622e636f6d2f55646f2f486f6d654f7665726c6f72642f6d61737465722f646f632f636c69656e74732e706e67" alt="" data-canonical-src="https://raw.github.com/Udo/HomeOverlord/master/doc/clients.png" style="max-width:100%;"></a></p>

<p>HomeOverlord allows you to assign specific settings on a per-IP-address basis, so you can for example control what a specific panel device will display.</p>

<p>Bring up the clients list to see a list of clients that have recently accessed the HomeOverlord user interface. Click on a specific client to change its settings. As of now, on that screen you can choose a common name for the client and select which rooms of your house apply to the client.</p>

<p>On the clients list screen you can also trigger a screen reload of all connected devices.</p>

<h1><a id="user-content-modes" class="anchor" href="#modes" aria-hidden="true"></a>Modes</h1>

<p><a href="https://camo.githubusercontent.com/ef99d1a11b657d229ccc4046160741b6a1eb679f/68747470733a2f2f7261772e6769746875622e636f6d2f55646f2f486f6d654f7665726c6f72642f6d61737465722f646f632f6d6f6465732e75692e706e67" target="_blank"><img src="https://camo.githubusercontent.com/ef99d1a11b657d229ccc4046160741b6a1eb679f/68747470733a2f2f7261772e6769746875622e636f6d2f55646f2f486f6d654f7665726c6f72642f6d61737465722f646f632f6d6f6465732e75692e706e67" alt="" data-canonical-src="https://raw.github.com/Udo/HomeOverlord/master/doc/modes.ui.png" style="max-width:100%;"></a></p>

<p>Modes are a way to change the behavior of devices according to a specific circumstance. For example, the Away mode could be used to make sure all non-essential devices are turned off.</p>

<p>To add or change the available modes, go the the admin screen and then select the "modes" option. On the 
modes page, you will see a list of modes, separated by line breaks.</p>

<h2><a id="user-content-mode-change-events" class="anchor" href="#mode-change-events" aria-hidden="true"></a>Mode Change Events</h2>

<p>When a mode gets activated, HomeOverlord will look for an event handler with the following naming scheme, where [name] is the name of the mode:</p>

<blockquote>
<p>MODE-[name]-ON</p>
</blockquote>

<p>the same applies when a mode is turned off:</p>

<blockquote>
<p>MODE-[name]-OFF</p>
</blockquote>
</article>
  
 </div>

<style>
	
.help-text {
	margin-top: 32px;
}

.help-text > h1 {
	font-size: 200%;
}
	
.help-text > p {
	margin-left:16px;
}

pre, code {
	font-family: Consolas, monospace;
}
	
pre {
	white-space: pre;
	margin-left: 32px;
	margin-bottom: 16px;
}
	
</style>


