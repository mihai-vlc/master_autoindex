<?php

// Master Autoindex
// ionutvmi@gmail.com 
// master-land.net

// for now a good old fashon grabber needs to be improved but time....



include "../inc/init.php";


$plugins->run_hook("plugin_market_top");

if(!is_admin()) {
	ob_end_clean();
	header("Location: $set->url");exit;
}

$links[] = mai_img("arr.gif")." <a href='index.php'>$lang->admincp </a>";
$links[] = mai_img("arr.gif")." <a href='?'>$lang->plugins_market</a>";


$act = $_GET['act'];

if(($act == 'install') && ($p_name = trim($_GET['p']))) {
    
    $tmp_name = "plugin".rand().".zip";
    
    if(copy("http://master-land.net/autoindex/lib/".$p_name, $tmp_name)) {
        
        $zip = new ZipArchive;
        
        // this algorithm should work fine but some extra testing is required
        // you can contribute on github.com/ionutvmi
        
        
        if ($zip->open($tmp_name) === true) {

            for($i = 0; $i < $zip->numFiles; $i++) { // we try (and hopefully succeed) to put the files in the correct folders

                    $filename = $zip->getNameIndex($i);
                    
                    $new_name = $filename;
                    
                    if(strpos($filename, "autoindex/") === 0)
                        $new_name = str_ireplace("autoindex/","", $filename);
                    
                    
                    if(trim($new_name) == '')
                        continue;
                    
                    $info = $zip->statIndex($i);
                    if($info['crc'] == 0) { // is dir
                        
                        if(!is_dir(MAI_ROOT.$new_name))
                            @mkdir(MAI_ROOT.$new_name,0777,true);
                            
                        continue;
                    }
                    
                    if(substr($filename, -11) == "_plugin.php") {
                       
                        copy("zip://".dirname(__FILE__)."/".$tmp_name."#".$filename, MAI_ROOT."plugins/".basename($filename));
                        continue;
                    }
                    
                    copy("zip://".dirname(__FILE__)."/".$tmp_name."#".$filename, MAI_ROOT.$new_name);

                }

            $zip->close();
        }
        
        $content .="<div class='green'>$lang->plugin_installed</div>";
    } else
        $content .="<div class='red'>$lang->error</div>";
    
    @unlink($tmp_name);
} else {

    
    $data = file_get_contents("http://master-land.net/autoindex/market.php?".$_SERVER['QUERY_STRING']);
    
    $data = preg_replace("#<!DOCTYPE(.+)<!--header end-->#iUs", "", $data);
    $data = preg_replace("#<!--footer start-->(.+)</html>#iUs", "", $data);
    $data = preg_replace("~<div class='content'><form action='#' method='post'>(.+)</div>~iUs", "", $data, 1);
    $data = preg_replace("~<div class='download'><a href='lib/(.+)'>(.+)</div>~i", "<a href='?act=install&p=$1'><div class='download'>$lang->install</div></a>", $data, 1);
    
    
    
    
    $content = $data. "<div class='content'>&#187; <a href='http://master-land.net/autoindex/market.php?add=1'>Add your plugin</a></div>";
}


include "../header.php";
$plugins->run_hook("plugin_market");
echo $content;
$plugins->run_hook("plugin_market_end");
include "../footer.php";