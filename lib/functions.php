<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net

function stripslashes_recursive($value) {
    if (is_array($value)) {
        foreach ($value as $index => $val) {
            $value[$index] = stripslashes_recursive($val);
        }
        return $value;
    } else {
        return stripslashes($value);
    }
}

function remove_magic_quotes()
{
    if( get_magic_quotes_gpc() ) {
		$_GET = stripslashes_recursive($_GET);
		$_POST = stripslashes_recursive($_POST);
    }
}


function mai_img($src,$alt = '') {
	global $plugins,$set;
	
	return $plugins->run_hook("mai_img","<img src='$set->url/". MAI_TPL ."style/images/$src' alt='$alt'>");
}
function mai_converturl($string){
	$string=str_replace(" ","-",$string);
	$string=str_replace(".","-",$string);
	$string=str_replace("@","-",$string);
	$string=str_replace("/","-",$string);
	$string=str_replace("\\","-",$string);
	$string=preg_replace("/[^a-zA-Z0-9\-]/", "", $string);
	return $string;
}
function is_admin(){
	global $set;
	if($_SESSION['adminpass'] == $set->sinfo->admin_pass)
		return true;
	return false;
}
function get_max_upl() {
$max_upload = (int)(ini_get('upload_max_filesize'));
$max_post = (int)(ini_get('post_max_size'));
$memory_limit = (int)(ini_get('memory_limit'));
return min($max_upload, $max_post, $memory_limit);
}
function convert($size)
{
$unit=array('B','KB','MB','GB','TB','PB');
return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
function tsince($t,$arr){
$tt=time() - $t;
$tp=$arr[0];
if ($tt>=60 && $tt<3600) {
$tt=floor($tt / 60); $tp=$arr[1]; }
if ($tt>=3600 && $tt<86400) {
$tt=floor($tt / 3600); 
$tp=$arr[2]; }
if ($tt>= 86400 && $tt < 2592000) {
$tt=floor($tt / 86400); 
if($tt=='1') {$tp=$arr[3];}else{$tp=$arr[4];} }
if ($tt >= 2592000) {
$tt=floor($tt / 2592000); 
if($tt=='1') {$tp=$arr[5];}else{$tp=$arr[6];} }

return "$tt $tp ";
}
function deleteAll($directory, $empty = false) { 
    if(substr($directory,-1) == "/") { 
        $directory = substr($directory,0,-1); 
    } 

    if(!file_exists($directory) || !is_dir($directory)) { 
        return false; 
    } elseif(!is_readable($directory)) { 
        return false; 
    } else { 
        $directoryHandle = opendir($directory); 
        
        while ($contents = readdir($directoryHandle)) { 
            if($contents != '.' && $contents != '..') { 
                $path = $directory . "/" . $contents; 
                
                if(is_dir($path)) { 
                    deleteAll($path); 
                } else { 
                    unlink($path); 
                } 
            } 
        } 
        
        closedir($directoryHandle); 

        if($empty == false) { 
            if(!rmdir($directory)) { 
                return false; 
            } 
        } 
        
        return true; 
    } 
} 

// remove by value:
function array_remove_value ()
{
  $args = func_get_args();
  return array_diff($args[0],array_slice($args,1));
}

function dirmv( $source, $destination ) {
	if ( is_dir( $source ) ) {
		@mkdir( $destination );
		$directory = dir( $source );
		while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
			if ( $readdirectory == '.' || $readdirectory == '..' ) {
				continue;
			}
			$PathDir = $source . '/' . $readdirectory; 
			if ( is_dir( $PathDir ) ) {
				dirmv( $PathDir, $destination . '/' . $readdirectory );
				continue;
			}
			rename( $PathDir, $destination . '/' . $readdirectory );
		}
 
		$directory->close();
	}else {
		rename( $source, $destination );
	}
}