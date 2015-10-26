#!/usr/bin/php
<?
error_reporting(0);

//TODO:


$pluginName ="BetaBriteMessage";
$BetaBritePluginName="BetaBrite";
$myPid = getmypid();

$DEBUG=false;

$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");
include_once("BetaBriteFunctions.inc.php");
//include_once("php_serial.class.php");
include_once("excluded_plugins.inc.php");
require ("lock.helper.php");
define('LOCK_DIR', '/tmp/');
define('LOCK_SUFFIX', '.lock');

       $BAUD = "9600";
        $PARITY="none";
        $CHAR_BITS="8";
        $STOP_BITS="1";

$messageQueue_Plugin = "MessageQueue";
$MESSAGE_QUEUE_PLUGIN_ENABLED=false;


$logFile = $settings['logDirectory']."/".$pluginName.".log";

$messageQueuePluginPath = $settings['pluginDirectory']."/".$messageQueue_Plugin."/";

$messageQueueFile = urldecode(ReadSettingFromFile("MESSAGE_FILE",$messageQueue_Plugin));

$messageQueueFunctionsFilename = "functions.inc.php";


if(($pid = lockHelper::lock()) === FALSE) {
	exit(0);

}

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;
if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);

	$MATRIX_PLUGIN_OPTIONS = $pluginSettings['PLUGINS'];
	logEntry("BetaBrite Message Plugins: ".$MATRIX_PLUGIN_OPTIONS);

//$ENABLED = trim(urldecode(ReadSettingFromFile("ENABLED",$pluginName)));
$ENABLED = $pluginSettings['ENABLED'];



if($ENABLED != "1") {
	logEntry("Plugin Status: DISABLED Please enable in Plugin Setup to use & Restart FPPD Daemon");
	lockHelper::unlock();
	exit(0);

}


$BetaBriteConfigFile = $settings['configDirectory'] ."/plugin." .$BetaBritePluginName;
if (file_exists($BetaBriteConfigFile))
	$BetaBritepluginSettings = parse_ini_file($BetaBriteConfigFile);

//print_r($BetaBritepluginSettings);

$DEVICE= $BetaBritepluginSettings['DEVICE'];
$DEVICE_CONNECTION_TYPE= $BetaBritepluginSettings['DEVICE_CONNECTION_TYPE'];

if(trim($DEVICE == "")) {
	logEntry("No BetaBrite Device is  configured for output: exiting");
	lockHelper::unlock();
	exit(0);
} else {
	logEntry("Configured BetaBrite Device name: ".$DEVICE);
	logEntry("Configured BetaBrite Device Type name: ".$DEVICE_CONNECTION_TYPE);
	
}


if(file_exists($messageQueuePluginPath."functions.inc.php"))
        {
                include $messageQueuePluginPath."functions.inc.php";
                $MESSAGE_QUEUE_PLUGIN_ENABLED=true;
                logEntry("message queue plugins functions loaded");

        } else {
                logEntry("Message Queue not installed, cannot use this plugin with out it");
                lockHelper::unlock();
                exit(0);
        }


if($MESSAGE_QUEUE_PLUGIN_ENABLED) {
		logEntry("getting new messages");
        $queueMessages = getNewPluginMessages($MATRIX_PLUGIN_OPTIONS);
        if($queueMessages != null || $queueMessages != "") {
        if($DEBUG)	{
        	print_r($queueMessages);
	}
		outputMessages($queueMessages);
        } else {
        	logEntry("No messages file exists??");
        }
        
} else {
        logEntry("MessageQueue plugin is not enabled/installed");
        lockHelper::unlock();
        exit(0);
}
//disableMatrixToolOutput();

lockHelper::unlock();

?>
