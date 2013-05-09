<?php

// Master Autoindex
// ionutvmi@gmail.com 
// master-land.net

// this is not using a tpl file because it's not meant to be edited by itself


include "../inc/init.php";


$plugins->run_hook("tpl_editor_top");

if(!is_admin()) {
	ob_end_clean();
	header("Location: $set->url");exit;
}

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." <a href='?'>$lang->tpl_editor</a>";


$act = $_GET['act'];

if($act == 'edit') {
    
    $file = MAI_ROOT."/".MAI_TPL."/".$_GET['f'];
    if(!file_exists($file))
        die("File does not exists !");
    
    $links[] = mai_img("arr.gif")." <a href='?act=edit&f=".urlencode(basename($file))."'>".basename($file)."</a>";
    
    if($_POST) 
        if(file_put_contents($file, $_POST['data'])) 
            $content .= "<div class='green'>$lang->saved</div>";
        else
            $content .= "<div class='red'>$lang->error</div>";
    
    
    
    $content .= "<div class='content'>
        <form action='#' method='post'>
            <textarea name='data'>".htmlentities(file_get_contents($file))."</textarea><br/>
            <input type='submit' name='ok' value='$lang->save'>
        </form>
    </div>";

} else {
    
    $files = glob(MAI_ROOT."/".MAI_TPL."*.tpl");
    if($files)
    foreach($files as $file)
        $content .= "<div class='content".(++$i%2==0 ? "2" : "")."'>&#187; <a href='?act=edit&f=".urldecode(basename($file))."'>".basename($file)."</a> ".convert(filesize($file))."</div>";
    $content .= "<div class='content".(++$i%2==0 ? "2" : "")."'> $lang->tpl_notice </div>";
}


include "../header.php";
$plugins->run_hook("tpl_editor");
echo $content;
$plugins->run_hook("tpl_editor_end");
include "../footer.php";