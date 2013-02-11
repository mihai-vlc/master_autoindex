<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net

include "inc/init.php";
include "lib/pagination.class.php";

$plugins->run_hook("index_top");

$dir  = (int)$_GET['dir'];

if($dir){
	$downloads_menu = $db->get_row("SELECT `name`,`path`,`description` FROM `". MAI_PREFIX ."files` WHERE `id` = '$dir'");
	
	if($downloads_menu->name != '')
		$lang->downloads_menu = $downloads_menu->name;
		
		
	foreach(explode('/',substr($downloads_menu->path,7)) as $dr){
		$_dr .="/".$dr;
		$id = $db->get_row("SELECT `id`,`name` FROM `". MAI_PREFIX ."files` WHERE `path` = '/files".$db->escape($_dr)."'");
		$links[] = mai_img("arr.gif")."&nbsp;<a href='$set->url/data/".$id->id."/".mai_converturl($id->name).".html'>".htmlentities($id->name)."</a>";
	}
	$title = $id->name;

}else{
	$title = $lang->Welcome;
	// updates

	$updates = "<b>$lang->updates </b><br/>";
	$up_data = $db->select("SELECT * FROM `". MAI_PREFIX ."files` WHERE size > 0 ORDER BY `id` DESC LIMIT 0,5");
	
	$plugins->run_hook("index_updates");
	if($up_data){
		foreach($up_data as $udata){
			$updates .= sprintf($lang->updates_text,"<a href='$set->url/data/file/$udata->id/".mai_converturl($udata->name).".html'>$udata->name</a>",tsince($udata->time,$lang->time_v));
		}
	}else
		$updates .= $lang->no_data;
}

if(is_admin()){
	$_admin = "<a href='$set->url/admincp/actions.php?act=edit&id=%1\$s'>$lang->edit</a> |
	<a href='$set->url/admincp/actions.php?act=delete&id=%1\$s'>$lang->delete</a> ";
	$_admin2 = "<div class='content'><a href='$set->url/admincp/actions.php?act=add&id=$dir'>$lang->add_folder</a></div>";
	
	$plugins->run_hook("index_admin");
}


$where_text = "`indir` = '$dir'";

if(!empty($_GET["search"])){
	$search_words = explode(" ", $_GET["search"]);
	foreach ($search_words as $search_word) {
		$where []= "`name` LIKE '%$search_word%'";
		$where2[]= "`description` LIKE '%$search_word%'";
	}
	$where_text = "(".implode(" AND ",$where).") OR (".implode(" AND ",$where2).") AND `size` > 0";
	$search_text = htmlentities($_GET["search"],ENT_QUOTES);
	$links[]=mai_img("arr.gif").$lang->search;
}elseif(!$dir)
	$links = ' ';

	$plugins->run_hook("index_search");
	
$total_results = $db->count("SELECT `id` FROM `". MAI_PREFIX ."files` WHERE $where_text");
if($total_results > 0) {

	// pagination
	$perpage = $_SESSION['perp'] ? (int)$_SESSION['perp'] : $set->perpage;
	$page = (int)$_GET['page'] == 0 ? 1 : (int)$_GET['page'];
	if($page > ceil($total_results/$perpage)) $page = ceil($total_results/$perpage);
	$start = ($page-1)*$perpage;

	$s_pages = new pag($total_results,$page,$perpage);
	$show_pages = $lang->pages.": ".$s_pages->pages;
	// order by
	if($_POST['sort'])
		$_SESSION['sort'] = (int)$_POST['sort'];
	if($_SESSION['sort'] === null) $_SESSION['sort'] = 6;
	
	switch($_SESSION['sort']){
		case 1 :
		$order = "`time` ASC"; $dateasc=" selected='1'"; break;
		case 2 :
		$order = "`name` DESC"; $namedesc=" selected='1'"; break;
		case 3 :
		$order = "`name` ASC"; $nameasc=" selected='1'";break;
		case 4 :
		$order = "`size` DESC"; $sizedesc=" selected='1'";break;
		case 5 :
		$order = "`size` ASC"; $sizeasc=" selected='1'";break;
		default :
		$order = "`time` DESC"; $datedesc=" selected='1'";
	}
		$show_order = "<form action='#' method='post'>$lang->sort 
		<select name='sort'>
		<option value='6'$datedesc>$lang->datedesc</option>
		<option value='1'$dateasc>$lang->dateasc</option>
		<option value='2'$namedesc>$lang->namedesc</option>
		<option value='3'$nameasc>$lang->nameasc</option>
		<option value='4'$sizedesc>$lang->sizedesc</option>
		<option value='5'$sizeasc>$lang->sizeasc</option>
		</select> <input type='submit' value='$lang->sort'></form>";
		
	$plugins->run_hook("index_order");
	
	$data = $db->select("SELECT * FROM `". MAI_PREFIX ."files` WHERE $where_text ORDER BY $order LIMIT $start,$perpage");


	foreach($data as $d){
		if($d->time > (time()-60*60*24)) 
			$new_text = "<span class='new'>($lang->new)</span>";
		else
			$new_text = '';
		if(is_dir(".".$d->path)){
			
			$count = $db->count("SELECT id from `". MAI_PREFIX ."files` WHERE `path` LIKE '".$d->path."%' AND `size` > 0");
			
			$plugins->run_hook("index_folders");
			
			$folders .= "<div class='content".(++$j%2==0 ? "2" : "")."'>
			<a href='$set->url/data/$d->id/".mai_converturl($d->name).".html'> <table><tr><td>
			".($d->icon != '' ? 
			"<img src='$set->url/thumb.php?ext&w=45&src=".urlencode($d->icon)."' alt='.'>" :
			"<img src='$set->url/thumb.php?w=45&src=".base64_encode("/". MAI_TPL ."style/images/folder.png")."' alt='.'/>"
			)."
			</td><td> $d->name $new_text <br/> $count $lang->files </td></tr></table></a>".sprintf($_admin,$d->id)." </div>";
			
			$plugins->run_hook("index_folders_end");
			
		}else{
			$plugins->run_hook("index_files_top");
			
			$files .= "<div class='content".(++$i%2==0 ? "2" : "")."'>
			<a href='$set->url/data/file/$d->id/".mai_converturl($d->name).".html'><table><tr><td>";
			
			// icon
			if($d->icon == '') {
				$ext = (object)pathinfo($d->path);
				$ext->extension = strtolower($ext->extension);
				
				if(in_array($ext->extension,array('png','jpg','jpeg','gif','jar'))) {
					if($ext->extension == 'jar') 
						$icon = "/icon.php?s=".base64_encode($d->path);
					else
						$icon = "/thumb.php?w=45&src=".base64_encode($d->path);
				}else{
					$all_icons = str_replace(".png","",array_map("basename",glob(MAI_TPL."style/png/*.png")));
					if(!in_array($ext->extension,$all_icons))
						$icon = "/". MAI_TPL ."style/png/file.png";
					else	
						$icon = "/". MAI_TPL ."style/png/$ext->extension.png";
				}
			} else {
				$icon = "/thumb.php?ext&w=45&src=".urlencode($d->icon);
			}
			
			$plugins->run_hook("index_files");
			
			$files .= "<img src='$set->url".$icon."' width='45'>";
			$files .= "</td><td>".$d->name." $new_text<br/>".convert($d->size)." </td></tr></table></a>".sprintf($_admin,$d->id)." </div>";
			
			$plugins->run_hook("index_files_end");
		}
	}
} else {
	$files = $lang->no_data;
}


// if the admin message is blank don't display the admin name
if(trim($set->sinfo->main_msg) == "")
	$lang->admin = null;
else{
	$lang->admin .= ":";
	$set->sinfo->main_msg .= "<br/><br/>";
}
include "header.php";
$tpl->grab('index.tpl','index');
$tpl->assign('MAI_TPL',$set->url."/".MAI_TPL);
$tpl->assign('url',$set->url);
$tpl->assign('admin',$lang->admin);
$tpl->assign('downloads_menu',$lang->downloads_menu);
$tpl->assign('description',$downloads_menu->description);
$tpl->assign('main_msg',$set->sinfo->main_msg);
$tpl->assign('updates',$updates);
$tpl->assign('files',$files);
$tpl->assign('folders',$folders);
$tpl->assign('extra',$lang->extra);
$tpl->assign('settings',$lang->settings);
$tpl->assign('show_pages',$show_pages);
$tpl->assign('search',$lang->search);
$tpl->assign('search_text',$search_text);
$tpl->assign('request',$lang->request);
$tpl->assign('show_order',$show_order);
$tpl->assign('_admin2',$_admin2);
$tpl->display();

$plugins->run_hook("index_end");

include "footer.php";

?>