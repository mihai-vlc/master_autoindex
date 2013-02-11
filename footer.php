<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
$plugins->run_hook("footer_top");

$footer = "<a href='$set->url'> $lang->Home </a> | <a href='$set->url/tos.php'>$lang->TOS</a> | <a href='$set->url/admincp'>$lang->admin_panel</a>";
if($_SESSION['adminpass'])
	$footer .= " | <a href='$set->url/logout.php'>$lang->logout </a>";

$tpl->grab('footer.tpl','footer');
$tpl->assign('footer',$footer);

$tpl->display();


$plugins->run_hook("footer_end");