<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
include "inc/settings.php";
session_start();
session_unset("adminpass");
$path_info = parse_url($set->url);
setcookie("pass", 0, time() - 3600 * 24 * 30, $path_info['path']); // delete

header("Location: ".$_SERVER["HTTP_REFERER"]);