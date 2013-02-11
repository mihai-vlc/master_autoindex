<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
session_start();
error_reporting(E_ALL ^ E_NOTICE); // hide the notice
define("MAI_ROOT",dirname(dirname(__FILE__)). "/");
define("MAI_TPL","tpl/");


$set = new stdClass(); // php 5.4 fix

include MAI_ROOT."inc/settings.php";
include MAI_ROOT."lib/mysql.class.php";
include MAI_ROOT."lib/plugin.class.php";
include MAI_ROOT."lib/template.class.php";
include MAI_ROOT."lang/index.php";
include MAI_ROOT."lib/functions.php";

// make $lang an object
$lang = (object)$lang;
// template object
$tpl = new Tpl();

// version
$set->version = '1.0.5';

// db connection
$db = new dbConn($set->db_host,$set->db_user,$set->db_pass,$set->db_name);
$set->sinfo = $db->get_row("SELECT * FROM `". MAI_PREFIX ."settings`");

if(!$set->sinfo){
	header("Location: install.php");
	exit;
}


if(!is_array(unserialize($set->sinfo->active_plugins)))
	$set->sinfo->active_plugins = serialize(array());

$_PS = $db->select("SELECT `name`,`value` FROM `".MAI_PREFIX."plugins_settings`");
if($_PS){
	foreach($_PS as $__PS){
		$set->plugins[$__PS->name] = $__PS->value;
	}
}

// plugins object
$plugins = new Plugins();
$plugins->load();




$links[] = mai_img("arr.gif")."&nbsp;<a href='$set->url'>$lang->Home</a>";

$plugins->run_hook("init");

remove_magic_quotes();