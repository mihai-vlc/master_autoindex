<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net
include "../inc/init.php";
$plugins->run_hook("admin_top");

$links[] = mai_img("arr.gif")." $lang->admincp";



if(((sha1($_POST['pass']) == $set->sinfo->admin_pass) && ($_POST['token'] == $_SESSION['token'])) OR is_admin()) {
	$_SESSION['token'] = '';
    
    if($_POST['r'] == 1) {
        $path_info = parse_url($set->url);
        setcookie("pass", sha1($_POST['pass']), time() + 3600 * 24 * 30, $path_info['path']); // 30 days
    }
    
	if(!$_SESSION['adminpass']){
		$_SESSION['adminpass'] = sha1($_POST['pass']);
	if($set->version != @file_get_contents("http://master-land.net/autoindex/updates.txt"))
		$update_av = "<div class='download'><a href='http://master-land.net/autoindex'>A NEW VERSION IS AVAILABLE</a></div>";
	}
	$request_new = "(".$db->count("SELECT `id` FROM `".MAI_PREFIX."request` WHERE `reply`=''").")";
    
    include "../header.php";	
	$tpl->grab('admin_options.tpl','admin_options');
	$tpl->assign('password',$lang->password);
	$tpl->assign('url',$set->url);
	$tpl->assign('import_files',$lang->import_files);
	$tpl->assign('settings',$lang->settings);
	$tpl->assign('update_av',$update_av);
	$tpl->assign('login',$lang->login);
	$tpl->assign('request',$lang->request);
	$tpl->assign('request_new',$request_new);
	$tpl->assign('file_manager',$lang->file_manager);
	$tpl->assign('plugin_manager',$lang->plugin_manager);
	$tpl->assign('tpl_editor',$lang->tpl_editor);
	$tpl->assign('upload_files',$lang->upload_files);
	$tpl->assign('mark',mai_img('arr.gif'));
	$tpl->assign('version',$set->version);
}else{
	$token = $_SESSION['token'] = md5(rand());
    
    include "../header.php";	
    $tpl->grab('admin_pass.tpl','admin_pass');
	$tpl->assign('password',$lang->password);
	$tpl->assign('token',$token);
	$tpl->assign('login',$lang->login);
	$tpl->assign('remember', $lang->remember);

}	
	$tpl->display();
$plugins->run_hook("admin_end");
include "../footer.php";

?>