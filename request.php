<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
include "inc/init.php";
include "lib/pagination.class.php";

$plugins->run_hook("request_top");

$links[] = mai_img("arr.gif")." $lang->request ";


if($_POST['rq']){
	$request_text = $_POST['rq'];
	if($request_text[10] && !$_COOKIE['ss']){
		$db->query("INSERT INTO `".MAI_PREFIX."request` SET `text`='".$db->escape($request_text)."'");
		$add = "<div class='green'> $lang->req_added </div>";
		setcookie('ss',md5(1),time()+3600*12);
		$plugins->run_hook("request_ins");
	}else{
		$add = "<div class='red'> $lang->req_limit </div>";
	}
}
if($_POST['reply'] && is_admin()){
	$plugins->run_hook("request_rpl");
	$db->query("UPDATE `".MAI_PREFIX."request` SET `reply` = '".$db->escape($_POST['reply'])."' WHERE `id`='".(int)$_POST['req']."'");
	header("Location: ?page=$page");
}
if($_GET['delete'] && is_admin()){
	$plugins->run_hook("request_del");
	$db->query("DELETE FROM `".MAI_PREFIX."request` WHERE `id`='".(int)$_GET['req']."'");
	header("Location: ?page=$page");
}

if(!is_admin())
	$where_text = "WHERE `reply` != ''";
// pagination
$total_results = $db->count("SELECT * FROM `".MAI_PREFIX."request` $where_text");
if($total_results > 0){
	$perpage = $_SESSION['perp'] ? (int)$_SESSION['perp'] : $set->perpage;
	$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
	if($page > ceil($total_results/$perpage)) $page = ceil($total_results/$perpage);
	$start = ($page-1)*$perpage;
	$s_pages = new pag($total_results,$page,$perpage);
	$show_pages = $lang->pages.": ".$s_pages->pages;

	$data = $db->select("SELECT * FROM `".MAI_PREFIX."request` $where_text ORDER BY `id` DESC LIMIT $start,$perpage");

	if($data){
		$requests = "<div class='title'>$lang->req_last</div>";
		foreach($data as $d){
			$requests .= "<div class='content".(++$i%2==0 ? "2" : "")."'>
			".nl2br(htmlentities($d->text))."<hr>";
			if((int)$_GET['req'] == $d->id && is_admin())
				$requests .= "<form action='?page=$page' method='post'><input type='hidden' name='req' value='$d->id'><input type='text' name='reply' value='".htmlentities($d->reply,ENT_QUOTES)."'><input type='submit' value='$lang->ok'><a href='?'>$lang->cancel</a></form>";
			else
				$requests .=($d->reply == '' ? "*" : "")."<u> $lang->admin : ".nl2br(htmlentities($d->reply))."</u>".(is_admin() ? " - <a href='?".$_SERVER['QUERY_STRING']."&req=$d->id'>$lang->reply</a> | <a href='?".$_SERVER['QUERY_STRING']."&delete=1&req=$d->id'>$lang->delete</a>" : "");
			
			$requests .="</div>";
		}
	}
}
include "header.php";
$tpl->grab("request.tpl","request");
$tpl->assign("requests",$requests);
$tpl->assign("add",$add);
$tpl->assign("show_pages",$show_pages);
$tpl->assign("lgrequest",$lang->request);
$tpl->display();

$plugins->run_hook("request_end");
include "footer.php";