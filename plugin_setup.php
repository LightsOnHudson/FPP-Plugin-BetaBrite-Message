<?php
include_once "/opt/fpp/www/common.php";
include_once "functions.inc.php";
include_once "commonFunctions.inc.php";


//$DEBUG=true;
$pluginName = "BetaBriteMessage";

$logFile = $settings['logDirectory']."/".$pluginName.".log";
$BetaBritePluginName = "BetaBrite";
$BetaBriteFunctionsFileName = "BetaBriteFunctions.inc.php";
$BETABRITE_PLUGIN_ENABLED=false;
$BetaBritePluginConfigFile = $settings['configDirectory']."/"."plugin.".$BetaBritePluginName;


$pluginUpdateFile = $settings['pluginDirectory']."/".$pluginName."/"."pluginUpdate.inc";


$gitURL = "https://github.com/LightsOnHudson/FPP-Plugin-BetaBrite-Message.git";

logEntry("plugin update file: ".$pluginUpdateFile);

if(file_exists($pluginDirectory."/".$BetaBritePluginName."/".$BetaBriteFunctionsFileName))
{
	logEntry($pluginDirectory."/".$BetaBritePluginName."/".$BetaBriteFunctionsFileName." EXISTS: Enabling");
	$BETABRITE_PLUGIN_ENABLED=true;
	
	if($DEBUG)
		logEntry("DEBUG: plugin config file: ".$BetaBritePluginConfigFile);
	
		if (file_exists($BetaBritePluginConfigFile)) {
			$BetaBritePluginSettings = parse_ini_file($BetaBritePluginConfigFile);
		
			if($DEBUG)
				print_r($BetaBritePluginSettings);
		}
		
	
	
	//createMatrixEventFile();
} else {
	logEntry("BetaBrite plugin is not installed, cannot use this plugin with out it");
	echo "BetaBrite plugin is not installed. Install the plugin and revisit this page to continue";
	exit(0);
	//exit(0);
}

if(isset($_POST['updatePlugin']))
{
	logEntry("updating plugin...");
	$updateResult = updatePluginFromGitHub($gitURL, $branch="master", $pluginName);

	echo $updateResult."<br/> \n";
}


if(isset($_POST['submit']))
{
	
	$PLUGINS =  implode(',', $_POST["PLUGINS"]);
//	echo "Writring config fie <br/> \n";
	WriteSettingToFile("PLUGINS",$PLUGINS,$pluginName);
	
	WriteSettingToFile("ENABLED",urlencode($_POST["ENABLED"]),$pluginName);
	WriteSettingToFile("FONT",urlencode($_POST["FONT"]),$pluginName);
	WriteSettingToFile("FONT_SIZE",urlencode($_POST["FONT_SIZE"]),$pluginName);

	WriteSettingToFile("COLOR",urlencode($_POST["COLOR"]),$pluginName);

	
	WriteSettingToFile("LAST_READ",urlencode($_POST["LAST_READ"]),$pluginName);
	WriteSettingToFile("MESSAGE_TIMEOUT",urlencode($_POST["MESSAGE_TIMEOUT"]),$pluginName);
	

}

	
	
	
//	$PLUGINS = urldecode(ReadSettingFromFile("PLUGINS",$pluginName));
$PLUGINS = $pluginSettings['PLUGINS'];
//	$ENABLED = urldecode(ReadSettingFromFile("ENABLED",$pluginName));
$ENABLED = $pluginSettings['ENABLED'];
//	$Matrix = urldecode(ReadSettingFromFile("MATRIX",$pluginName));

$LAST_READ = $pluginSettings['LAST_READ'];
$FONT= $pluginSettings['FONT'];
$FONT_SIZE= $pluginSettings['FONT_SIZE'];
$PIXELS_PER_SECOND= $pluginSettings['PIXELS_PER_SECOND'];
$COLOR= urldecode($pluginSettings['COLOR']);


?>

<html>
<head>
</head>

<div id="<?echo $pluginName;?>" class="settings">
<fieldset>
<legend><?php echo $pluginName;?> Support Instructions</legend>

<p>Known Issues:
<ul>
<li>NONE</li>
</ul>
<p>Configuration:
<ul>
<li>This plugin allows you to use the BetaBrite plugin to output messages from the MessageQueue system</li>
<li>Select your plugins to output to your BetaBrite below and click SAVE</li>
<li>Configure your BetaBrite first before selecting here</li>
</ul>



<form method="post" action="http://<? echo $_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']?>/plugin.php?plugin=<?echo $pluginName;?>&page=plugin_setup.php">


<?
echo "<input type=\"hidden\" name=\"LAST_READ\" value=\"".$LAST_READ."\"> \n";
$restart=0;
$reboot=0;

echo "ENABLE PLUGIN: ";

if($ENABLED== 1 || $ENABLED == "on") {
		echo "<input type=\"checkbox\" checked name=\"ENABLED\"> \n";
//PrintSettingCheckbox("Radio Station", "ENABLED", $restart = 0, $reboot = 0, "ON", "OFF", $pluginName = $pluginName, $callbackName = "");
	} else {
		echo "<input type=\"checkbox\"  name=\"ENABLED\"> \n";
}

echo "<p/> \n";


echo "BetaBrite Device Change in the BetaBrite Plugin: ";

echo "<b>/dev/".$BetaBritePluginSettings['DEVICE']."</b> \n";

echo "<p/>\n";

echo "Include Plugins in Matrix output: \n";
printPluginsInstalled();

echo "<p/> \n";

?>
<p/>
<input id="submit_button" name="submit" type="submit" class="buttons" value="Save Config">
<?
 if(file_exists($pluginUpdateFile))
 {
 	//echo "updating plugin included";
	include $pluginUpdateFile;
}
?>
<p>To report a bug, please file it against <?php echo $gitURL;?>
</form>

</fieldset>
</div>
<br />
</html>

?>