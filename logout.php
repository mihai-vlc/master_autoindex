<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
session_start();
session_destroy();
setcookie("pass", 0, time() - 3600 * 24 * 30, $path_info['path']); // delete

header("Location: ".$_SERVER["HTTP_REFERER"]);