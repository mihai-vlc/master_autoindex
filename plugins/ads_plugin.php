<?php
/**
* adverts plugin - it will display html ads in header and/or footer
* author: ionutvmi@gmail.com
* 21-Sep-2012
* this can be used as reference for making a settings page in Plugin Manager
* settings type:
*	yesno
*	onoff
*	textarea
*	text
*	select \n 1=ok \n 2=no
*	radio \n 1=ok \n 2=no
*	checkbox \n 1=top 2=bottom
*
*/


$plugins->add_hook("header","ads_show_top");
$plugins->add_hook("footer","ads_show_foot");

function ads_info(){

	return	array(	
	"name" => "Adverts Plugin",
	"author" => "ionutvmi",
	"author_site" => "http://master-land.net",
	"description" => "it will display html ads in header and/or footer",
	);
	
}

function ads_install(){
	global $db;
	// settings 
	$settings_data = array(
	"name" => "ads_show", // name of the setting must be unique so adding the plugin name is a good practice
	"value" => "2", // default value
	"title" => "Place ads on:", // title will be displayed on settings page
	"description" => "the place where the ads will be displayed", // description
	"type" => "select \n 0=Top \n 1=Bottom \n 2=Both", // type check master-land.net for more info
	"plugin" => "ads", // your plugin <name>
	);
	$settings_data2 = array(
	"name" => "ads_show_text_top", 
	"value" => "Top ad text", 
	"title" => "Top Ad", 
	"description" => "the ad content that will be placed in header", 
	"type" => "textarea",
	"plugin" => "ads", 
	);
	$settings_data3 = array(
	"name" => "ads_show_text_foot", 
	"value" => "Footer ad text", 
	"title" => "Footer Ad", 
	"description" => "the ad content that will be placed in footer", 
	"type" => "textarea",
	"plugin" => "ads", 
	);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data2);
	$db->insert_array(MAI_PREFIX."plugins_settings",$settings_data3);

}

function ads_is_installed(){
	global $db;
	if($db->count("SELECT `name` FROM `".MAI_PREFIX."plugins_settings` WHERE `plugin`='ads'") > 0)
		return true;
	
	return false;
}

function ads_uninstall(){
	global $db;
	$db->query("DELETE FROM `".MAI_PREFIX."plugins_settings` WHERE `plugin`='ads'");
}

// no special activate/deactivate required here

function ads_show_top($value){
	global $db,$set;
	
	if($set->plugins["ads_show"] == '0' OR $set->plugins["ads_show"] == '2')
		$value = str_replace("<!--header end-->","<!--header end-->".$set->plugins["ads_show_text_top"],$value);
	
	return $value;
}

function ads_show_foot($value){
	global $db,$set;
	
	if($set->plugins["ads_show"] == '1' OR $set->plugins["ads_show"] == '2')
		$value = str_replace("<!--footer start-->","<!--footer start-->".$set->plugins["ads_show_text_foot"],$value);
	
	return $value;
}