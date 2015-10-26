#!/usr/bin/php
<?
error_reporting(0);

//TODO:


$pluginName ="BetaBriteMessage";
$BetaBritePluginName="BetaBrite";
$myPid = getmypid();

$DEBUG=true;

$skipJSsettings = 1;
include_once("/opt/fpp/www/config.php");
include_once("/opt/fpp/www/common.php");
include_once("functions.inc.php");

include_once("excluded_plugins.inc.php");
require ("lock.helper.php");
define('LOCK_DIR', '/tmp/');
define('LOCK_SUFFIX', '.lock');


$messageQueue_Plugin = "MessageQueue";
$MESSAGE_QUEUE_PLUGIN_ENABLED=false;


$logFile = $settings['logDirectory']."/".$pluginName.".log";

$messageQueuePluginPath = $settings['pluginDirectory']."/".$messageQueue_Plugin."/";

$messageQueueFile = urldecode(ReadSettingFromFile("MESSAGE_FILE",$messageQueue_Plugin));

$messageQueueFunctionsFilename = "functions.inc.php";






if(($pid = lockHelper::lock()) === FALSE) {
	exit(0);

}
$BetaBriteFunctionFileName = "BetaBriteFunctions.inc.php";
$BetaBriteFunctionFile= $settings['pluginDirectory'] ."/" .$BetaBritePluginName."/".$BetaBriteFunctionFileName;
if (file_exists($BetaBriteFunctionFile)) {
	include_once $BetaBriteFunctionFile;
} else {
	logEntry("BetaBrite Function file: ".$BetaBriteFunctionFileName." does not exist. EXITING");
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

$pluginConfigFile = $settings['configDirectory'] . "/plugin." .$pluginName;

if (file_exists($pluginConfigFile))
	$pluginSettings = parse_ini_file($pluginConfigFile);

$BetaBriteConfigFile = $settings['configDirectory'] ."/plugin." .$BetaBritePluginName;
if (file_exists($BetaBriteConfigFile))
	$BetaBritepluginSettings = parse_ini_file($BetaBriteConfigFile);


$BetaBrite = $BetaBritepluginSettings['DEVICE'];

if(trim($BetaBrite == "")) {
	logEntry("No BetaBrite Device is  configured for output: exiting");
	lockHelper::unlock();
	exit(0);
} else {
	logEntry("Configured BetaBrite Device name: ".$BetaBrite);
	
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
        	
        //print_r($queueMessages);
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
