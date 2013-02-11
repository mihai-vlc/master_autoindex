<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
include "../inc/init.php";
include "../lib/Snoopy.class.php";
$plugins->run_hook("admin_import_form_top");

if(!is_admin()) {
	header("Location: $set->url");exit;
}

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." $lang->import_files ";
$message = '';

$all_folders = $db->select("SELECT `path` FROM `". MAI_PREFIX ."files` WHERE `size` = '0'");

foreach($all_folders as $folder){
	$folder = substr($folder->path,6); // remove /files
	$path_opt .= "<option value='$folder'>$folder</option>";
}
if($_POST){

$path = "../files".$_POST['path'];

	$i=0;
	foreach($_POST['f'] as $f){
		if(trim($_POST['n'][$i]) == '')
			$_name = preg_match("~(.*)\.(\w)~i",basename($f)) ? basename($f) : "file_$i.dat";
		else
			$_name = preg_match("~(.*)\.(\w)~i",basename($_POST['n'][$i])) ? basename($_POST['n'][$i]) : "file_$i.dat";
		$plugins->run_hook("admin_import_form_post");
		if($f !='' AND $f !='http://' AND !file_exists($path."/".$_name)){
			$ext = (object)pathinfo($_name);
			if($ext->extension == 'jad'){
			
				$lines = file($f);
				foreach($lines as $line)
					if(strpos($line,"MIDlet-Jar-URL:") !== FALSE)
						$url = trim(str_replace("MIDlet-Jar-URL:","",$line));
				
				$plugins->run_hook("admin_import_form_jad_top");
				
				if($url){

					$snoopy = new Snoopy;
					if($snoopy->fetch($url)){ 
					$_name = preg_match("~(.*)\.(\w)~i",basename($url)) ? basename($url) : "game_$i.jar";
						fopen($path."/".$_name,"w");
						if(file_put_contents($path."/".$_name,$snoopy->results)){
							$message .= "<div class='green'>".$_name.$lang->file_uploaded."</div>";
							$dirid = $db->get_row("SELECT id FROM `". MAI_PREFIX ."files` WHERE `path`='".substr($path,2)."'")->id;
							if($dirid != 0) {
								foreach(explode('/',substr($path,9)) as $dr){
									$_dr .="/".$dr;
									if($_dr != '/')
									$db->query("UPDATE `". MAI_PREFIX ."files` SET `time`='".time()."' WHERE `path` = '/files$_dr'");
								}
							}
							
							$db->insert("INSERT INTO `". MAI_PREFIX ."files` SET `name`='".$db->escape($_name)."', `path`='".substr($path,2)."/".$db->escape($_name)."',`indir`='$dirid', `time`='".time()."',`size`='".filesize($path."/".$_name)."'");
							$plugins->run_hook("admin_import_form_jad");
						}
					}
				}
			}else{
				$plugins->run_hook("admin_import_form_mid_top");
				$snoopy = new Snoopy;
				if($snoopy->fetch($f)){
					fopen($path."/".$_name,"w");
					file_put_contents($path."/".$_name,$snoopy->results);
					$message .= "<div class='green'>".$_name.$lang->file_uploaded."</div>";
					$dirid = $db->get_row("SELECT id FROM `". MAI_PREFIX ."files` WHERE `path`='".substr($path,2)."'")->id;
					
					if($dirid != 0) {
						foreach(explode('/',substr($path,9)) as $dr){
							$_dr .="/".$dr;
							if($_dr != '/')
							$db->query("UPDATE `". MAI_PREFIX ."files` SET `time`='".time()."' WHERE `path` = '/files$_dr'");
						}
						
					}
					$db->insert("INSERT INTO `". MAI_PREFIX ."files` SET `name`='".$db->escape($_name)."', `path`='".substr($path,2)."/".$db->escape($_name)."',`indir`='$dirid', `time`='".time()."',`size`='".filesize($path."/".$_name)."'");
					$plugins->run_hook("admin_import_form_mid");
				}else
					$message .= "<div class='red'>".$_FILES['f']['name'][$i].$lang->file_not_uploaded."</div>";
			}
			
		}
		++$i;
	}
}


include "../header.php";
$tpl->grab("admin_import_form.tpl","admin_import_form");
$tpl->assign("import_files",$lang->import_files);
$tpl->assign("url",$lang->url);
$tpl->assign("name",$lang->name);
$tpl->assign("message",$message);
$tpl->assign("path_opt",$path_opt);
$tpl->display();
$plugins->run_hook("admin_import_form_end");
include "../footer.php";

?>