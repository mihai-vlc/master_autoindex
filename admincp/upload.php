<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
include "../inc/init.php";
include "../lib/Snoopy.class.php";
$plugins->run_hook("admin_upload_form_top");

if(!is_admin()) {
	header("Location: $set->url");exit;
}

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." $lang->upload_files ";
$maximum = get_max_upl();
$message = '';

$all_folders = $db->select("SELECT `path` FROM `". MAI_PREFIX ."files` WHERE `size` = '0'");

foreach($all_folders as $folder){
	$folder = substr($folder->path,6); // remove /files
	$path_opt .= "<option value='$folder'>$folder</option>";
}
if($_FILES){

$path = "../files".$_POST['path'];

	$i=0;
	foreach($_FILES['f']['name'] as $f){
		
		$plugins->run_hook("admin_upload_form_post");
		
		if(file_exists($_FILES['f']['tmp_name'][$i]) AND !file_exists($path."/".$_FILES['f']['name'][$i])){
			$ext = (object)pathinfo($_FILES['f']['name'][$i]);
			if((($_FILES['f']['size'][$i]/1024)/1024) < $maximum){
				if($ext->extension == 'jad'){
					$lines = file($_FILES['f']['tmp_name'][$i]);
					@unlink($_FILES['f']['tmp_name'][$i]);
					foreach($lines as $line)
						if(strpos($line,"MIDlet-Jar-URL:") !== FALSE)
							$url = trim(str_replace("MIDlet-Jar-URL:","",$line));
							
					$plugins->run_hook("admin_upload_form_jad_top");
							
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
								$plugins->run_hook("admin_upload_form_jad");
							}
						}
					}
				}else{
				$plugins->run_hook("admin_upload_form_mid_top");
					if(move_uploaded_file($_FILES['f']['tmp_name'][$i],$path."/".$_FILES['f']['name'][$i])){ 
						$message .= "<div class='green'>".$_FILES['f']['name'][$i].$lang->file_uploaded."</div>";
						$dirid = $db->get_row("SELECT id FROM `". MAI_PREFIX ."files` WHERE `path`='".substr($path,2)."'")->id;
						
						if($dirid != 0) {
							foreach(explode('/',substr($path,9)) as $dr){
								$_dr .="/".$dr;
								if($_dr != '/')
								$db->query("UPDATE `". MAI_PREFIX ."files` SET `time`='".time()."' WHERE `path` = '/files$_dr'");
							}
							
						}
						$db->insert("INSERT INTO `". MAI_PREFIX ."files` SET `name`='".$db->escape($_FILES['f']['name'][$i])."', `path`='".substr($path,2)."/".$db->escape($_FILES['f']['name'][$i])."',`indir`='$dirid', `time`='".time()."',`size`='".$_FILES['f']['size'][$i]."'");
						$plugins->run_hook("admin_upload_form_mid");
					}else
						$message .= "<div class='red'>".$_FILES['f']['name'][$i].$lang->file_not_uploaded."</div>";
				}
			}else {
				@unlink($_FILES['f']['name'][$i]);
				$message .= "<div class='red'>".$_FILES['f']['name'][$i].$lang->file_too_big."</div>";
			}
	
		}
		++$i;
	}
}
$maximum .= "M";

include "../header.php";
$tpl->grab("admin_upload_form.tpl","admin_upload_form");
$tpl->assign("upload_files",$lang->upload_files);
$tpl->assign("message",$message);
$tpl->assign("path_opt",$path_opt);
$tpl->assign("max_file_size",$lang->max_file_size.$maximum);
$tpl->display();

$plugins->run_hook("admin_upload_form_end");
include "../footer.php";

?>