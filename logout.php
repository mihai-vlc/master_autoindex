<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
session_start();
session_destroy();


header("Location: ".$_SERVER["HTTP_REFERER"]);