<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net

$plugins->run_hook("header_top");

if(!$title)
	$title = strip_tags(end($links));


$title = $title." - ". $set->name;
$logo = empty($set->logo) ? $set->name : "<img src='$set->logo' alt='logo'>";

// $links should be defined in inc/init.php as an array
if(is_array($links))
	foreach($links as $link)
		$_links .= $link." ";

$tpl->grab('header.tpl','header');
$tpl->assign('title',$title);
$tpl->assign('logo',$logo);
$tpl->assign('url',$set->url);
$tpl->assign('links',$_links);
$tpl->display();

$plugins->run_hook("header_end");