<html>
	<head>
		<title>master-land.net installer</title>
		<style>
			h4 {
				color:white;
				background-color: black;
				padding: 3px;
				text-align:center;
			}
		</style>
	</head>
	<body>
		<h4>MASTER AUTOINDEX INSTALLER </h4>
<?php
if($_POST){

$fp = fopen('inc/settings.php','w');

$content = '
<?php
// Master Autoindex
// ionutvmi@gmail.com 
// Sep 2012
// master-land.net

$set->db_name = "'.$_POST['name'].'";
$set->db_user = "'.$_POST['user'].'";
$set->db_host = "'.$_POST['host'].'";
$set->db_pass = "'.$_POST['pass'].'";


$set->name = "'.$_POST['site_name'].'"; // site name
$set->url = "'.$_POST['site_url'].'"; // site url
$set->logo = "'.$_POST['site_logo'].'"; // logo url (full url http://site.com/logo.png)
$set->perpage = "10"; // how many records per page
define("MAI_PREFIX","'.$_POST['prefix'].'");
';

if(!fwrite($fp,trim($content)))
	$error = 1;

fclose($fp);

include "inc/settings.php";

include "lib/mysql.class.php";

$db = new dbConn($set->db_host,$set->db_user,$set->db_pass,$set->db_name);


if(!$db->query("CREATE TABLE IF NOT EXISTS `".$_POST['prefix']."files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `path` text NOT NULL,
  `indir` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL,
  `dcount` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `size` int(11) NOT NULL,
  `icon` text NOT NULL,
  `description` text NOT NULL,
  `isdir` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))	$error = 1;
if(!$db->query("INSERT INTO `".$_POST['prefix']."files` (`id`, `name`, `path`, `indir`, `views`, `dcount`, `time`, `size`, `icon`,`isdir`) VALUES
(1, 'Games', '/files/Games', 0, 0, 0, 1348259936, 0, '', 1);"))	$error = 1;
if(!$db->query("CREATE TABLE IF NOT EXISTS `".$_POST['prefix']."plugins_settings` (
  `name` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `type` text NOT NULL,
  `plugin` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;"))	$error = 1;
if(!$db->query("CREATE TABLE IF NOT EXISTS `".$_POST['prefix']."request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `reply` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"))	$error = 1;
if(!$db->query("CREATE TABLE IF NOT EXISTS `".$_POST['prefix']."settings` (
  `admin_pass` varchar(100) NOT NULL,
  `main_msg` text NOT NULL,
  `active_plugins` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;"))	$error = 1;
if(!$db->query("INSERT INTO `".$_POST['prefix']."settings` (`admin_pass`, `main_msg`, `active_plugins`) VALUES
('".sha1(trim($_POST['admin_pass']))."', 'Welcome to our site !\r\nHope you enjoy it :D', 'a:0:{}');"))	$error = 1;

if($error){
echo "Some error camed up. Check if /inc/settings.php is writable and if your prefix is correct.";
}else {
echo "<h1>Installation Complete</h1>
<meta http-equiv='refresh' content='5; url=index.php'>";

// @unlink(__FILE__);
}

}else{

@chmod("files",0777);
@chmod("inc/settings.php",0666);

echo "<form action='?' method='post'>

Database Host<br/><input type='text' name='host' value='localhost'><br/>
Database User<br/><input type='text' name='user'><br/>
Database Password<br/><input type='text' name='pass'><br/>
Database Name<br/><input type='text' name='name'><br/>
Table Prefix: <br/><input type='text' name='prefix' value='mai_'><br/>
Site Name<br/><input type='text' name='site_name' value='Master Autoindex'><br/>

Site Logo(keep blank if you don't have)<br/><input type='text' name='site_logo'><br/>
Site Url<br/><input type='text' name='site_url' value='http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."'><br/>
Admin Pass: <br/><input type='text' name='admin_pass' value='12345'><br/>
<br/><input type='submit' value='Install'>
</form>";

}
?>
		<h4>www.master-land.net</h4>
	</body>
</html>