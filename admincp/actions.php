<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
include "../inc/init.php";

$plugins->run_hook("admin_actions_top");


if(!is_admin()) {
	header("Location: $set->url");exit;
}
$fid = (int)$_GET['id'];


$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." <a href='$set->url/index.php'>$lang->file_manager </a>";

// add
if($_GET['act'] == 'add') {
	$file = $db->get_row("SELECT * FROM `". MAI_PREFIX ."files` WHERE `id`='$fid'");
	if(!$file)
		$file->path = "/files";
	if(!is_dir("..".$file->path)){
		header("Location: $set->url");exit;
	}
	
	$plugins->run_hook("admin_actions_add_top");
	
	if($_POST['name']){
		if($db->count("SELECT `id` FROM `". MAI_PREFIX ."files` WHERE `path` = '".$file->path."/".$_POST['name']."'") == 0) {
			if($db->insert("INSERT INTO `". MAI_PREFIX ."files` SET `name`='".$db->escape($_POST['name'])."',`path`='".$db->escape($file->path."/".$_POST['name'])."', `icon`='".$db->escape($_POST['icon'])."',`indir`='".(int)$_GET['id']."', `time`='".time()."'")){
				mkdir("..".$file->path."/".$_POST['name'],0777);
				
				$plugins->run_hook("admin_actions_add");
				
			//	$form .= "<div class='green'>$lang->added</div>";
			header("Location: $set->url/data/".(int)$_GET['id']."/$file->name.html");
			}
		}
	}
	$links[] = mai_img("arr.gif")." $lang->add ";

	$form .= "<form action='#' method='post'>
		$lang->name : <input type='text' name='name' value='new'><br/>
		$lang->icon : <input type='text' name='icon'><br/>
		<br/>
		<input type='submit' value='$lang->add'>
	</form>";
	$plugins->run_hook("admin_actions_add_end");
}
// edit
if($_GET['act'] == 'edit') {
	$file = $db->get_row("SELECT * FROM `". MAI_PREFIX ."files` WHERE `id`='$fid'");
	if(!$file) {
		header("Location: $set->url");
		exit;
	}
	$plugins->run_hook("admin_actions_edit_top");
	
	if($file->size > 0)
		$links[] = mai_img("arr.gif")." <a href='$set->url/data/file/$file->id/".mai_converturl($file->name).".html'>$file->name </a>";
	else
		$links[] = mai_img("arr.gif")." <a href='$set->url/data/$file->id/".mai_converturl($file->name).".html'>$file->name </a>";
	if($_POST['name']){
		$path = "/files".$_POST['path'];
		$dirid = $db->get_row("SELECT id FROM `". MAI_PREFIX ."files` WHERE `path`='".$path."'")->id;
		$real_path = $path."/".basename($file->path);
		if($db->query("UPDATE `". MAI_PREFIX ."files` SET `name`='".$db->escape($_POST['name'])."', `icon`='".$db->escape($_POST['icon'])."', `indir`='".$dirid."', `path`= '".$db->escape($real_path)."', `description`='".$db->escape($_POST['description'])."' WHERE `id`='$file->id'")){
		
		if($file->path != $real_path){
			if(is_file("..".$file->path)){
				rename("..".$file->path,"..".$real_path);
			}else{
				dirmv("..".$file->path,"..".$real_path);
				$db->query("UPDATE `". MAI_PREFIX ."files` SET `path`=replace(`path`,'".$db->escape($file->path)."','".$db->escape($real_path)."') WHERE `path` LIKE '".$db->escape($file->path)."%'");
			}
		}
		$form .= "<div class='green'>$lang->saved</div>";
		$file->icon = $_POST['icon']; // to keep it updated
		$file->name = $_POST['name']; // to keep it updated
		$file->path = $real_path; // to keep it updated
		$file->description = $_POST['description']; // to keep it updated
		$plugins->run_hook("admin_actions_edit");
		}
	}
	$links[] = mai_img("arr.gif")." $lang->edit ";

	$form .= "<form action='#' method='post'>
		$lang->name : <input type='text' name='name' value='".htmlentities($file->name,ENT_QUOTES)."'><br/>
		$lang->icon : <input type='text' name='icon' value='".htmlentities($file->icon,ENT_QUOTES)."'><br/>
		$lang->description :<br/> <textarea name='description'>".htmlentities($file->description)."</textarea><br/>
		$lang->path: <select name='path'><option value=''>./</option>";
		$all_folders = $db->select("SELECT `path` FROM `". MAI_PREFIX ."files` WHERE `size` = '0'");

		foreach($all_folders as $folder){
			$folder2 = substr($folder->path,6); // remove /files
			
			if(dirname($file->path) === $folder->path)
				$selected = " selected='vmi'";
			else
				$selected = '';
				
			$form .= "<option value='$folder2'$selected>$folder2</option>";
		}
		
		$form .= "</select>/".basename($file->path)."<br/>
		<input type='submit' value='$lang->save'>
	</form>";
	$plugins->run_hook("admin_actions_edit_end");
}
// edit settings
if($_GET['act'] == 'editset') {
	$plugins->run_hook("admin_actions_editset_top");

	if($_POST['msg']){
		if(trim($_POST['pass']) != ''){
			$pass = ", `admin_pass` = '".sha1($_POST['pass'])."'";
			$_SESSION['adminpass'] = sha1($_POST['pass']);
			}
		if($db->query("UPDATE `". MAI_PREFIX ."settings` SET `main_msg`='".$db->escape($_POST['msg'])."' $pass")){
		$form .= "<div class='green'>$lang->saved</div>";
		$set->sinfo->main_msg = $_POST['msg']; // to keep it updated
		$plugins->run_hook("admin_actions_editset");
		}
	}
	$links[] = mai_img("arr.gif")." $lang->settings ";

	$form .= "<form action='#' method='post'>
		$lang->main_msg :<br/> <textarea name='msg'>".htmlentities($set->sinfo->main_msg,ENT_QUOTES)."</textarea><br/>
		$lang->password ($lang->keep_blank):<br/> <input type='password' name='pass'><br/>
		<br/>
		<input type='submit' value='$lang->save'>
	</form>";
	$plugins->run_hook("admin_actions_editset_end");
}
//delete
if($_GET['act'] == 'delete') {
	$file = $db->get_row("SELECT * FROM `". MAI_PREFIX ."files` WHERE `id`='$fid'");
	if(!$file) {
		header("Location: $set->url");
		exit;
	}	
	$plugins->run_hook("admin_actions_delete_top");
	if($file->size > 0)
		$links[] = mai_img("arr.gif")." <a href='$set->url/data/file/$file->id/".mai_converturl($file->name).".html'>$file->name </a>";
	else
		$links[] = mai_img("arr.gif")." <a href='$set->url/data/$file->id/".mai_converturl($file->name).".html'>$file->name </a>";
	$links[] = mai_img("arr.gif")." $lang->delete ";
	if($_POST['yes']){
		if(is_dir("..".$file->path)){
			deleteAll("..".$file->path);
			$db->query("DELETE FROM `". MAI_PREFIX ."files` WHERE `path` LIKE '$file->path%'");
			$plugins->run_hook("admin_actions_delete_a");
		}else {
			@unlink("..".$file->path);
			$db->query("DELETE FROM `". MAI_PREFIX ."files` WHERE `id`='$file->id'");
			$plugins->run_hook("admin_actions_delete_b");
		}
		$form = "<div class='green'>$lang->data_gone</div>";
	}else {
		$form .="<form action='#' method='post'>
		$lang->are_you_sure <br/>
			<input type='submit' name='yes' value='$lang->yes'> <a href='$set->url'> $lang->no </a>
		</form>";
	}
	$plugins->run_hook("admin_actions_delete_end");
}

include "../header.php";
$tpl->grab("admin_actions.tpl","admin_actions");
$tpl->assign("form",$form);
$tpl->display();

$plugins->run_hook("admin_actions_end");

include "../footer.php";