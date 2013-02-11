<?php
/**
* The name of the function must be look this <name>_<action>
* where the <name> is same as in file name <name>_plugin.php 
* in our case <name> is hello
* this is mandatory only for default functions
* Default function list: _info(), _install(), _uninstall(), _is_installed(), _activate, _deactivate
* all this functions are optional a plugin can work just fine without any of this functions
* BUT I RECOMMEND YOU USE THEM (if needed of course)




* Hooks
* Tho find a hook name you open the file that you want to edit
* example `header.php` and you search for something like
* $tpl->grab('header.tpl','header'); or like $plugins->run_hook("header_top");
* ********************************
* On the first case the first argument is the template file name and the second is the hook
* now if you want to run your function at that point or on that the difference that
* you need to note is that the one called using grab() has to return
* a value witch will be the teplate file content. Your function should look like this
* function hello_dosomething($value){
*  // do something
*	return $value;
* } 
* ********************************
* On the seccond run_hook() only the name of the hook is defined you don't
* have to return anything and your function will look like
* function hello_dosomething(){
*  // do something
* }
*
*
* hope you spot the difference if you still need help check other plugins or 
* ask for help on www.master-land.net
*/


// you can use as many hooks as you need
// now we are interested in the first case were the hook name is `header` 
// so we need to add our function to be executed in that place
// as you can notice the first is the hook name and the second is our function name


$plugins->add_hook("header","hello_show_text");



//// Now let's build our plugin

// we first provide some info about the plugin
function hello_info(){
	/**
	* Array containing info about your plugin
	* name - plugin name witch will be displayed in Manager
	* author - author name(s)
	* author_site - author site if you don't have keep it blank
	* description - a very small description about this plugin
	*/
	return	array(	
	"name" => "Hello World",
	"author" => "ionutvmi",
	"author_site" => "http://master-land.net",
	"description" => "this is a demo plugin, it will show a text on the top of the page",
	);
}
 
// function hello_install() {
	// /**
	// * This should install the plugin. Add or alter tables, text files etc.
	// * It will be called in the manager on admin request.
	// */
	// return true;
// }
// function hello_uninstall() {
	// /**
	// * This should uninstall the plugin. Remove or alter tables, text files etc.
	// * It will be called in the manager on admin request.
	// */
	// return true;
// }

// function hello_is_installed(){
	// /**
	// * This ONLY checks if the plugin is installed
	// */
	// return true;
// }

// function hello_activate(){
	// /**
	// * This should make the plugin 'visible' 
	// * by making the required changes on template vars etc..
	// */
	// return true;
// }
// function hello_deactivate(){
	// /**
	// * This should 'hide' the plugin but keep all the data such as tables
	// * or txt files . the should be removed only by _uninstall()
	// * it will be also called before _uninstall() if the plugin is active
	// */
	// return true;
// }



// in this particular case we don't have anything to install so we don't need to define the functions that handle that


// now let's build our function
// it's not mandatory for the function to start with the plugin name
// but it's a good practice so we don't have problems with other plugins

// it called using grab() so we need to return the `value` 
function hello_show_text($value){
	global $set;
	// $value is the template content before the vars are replaced
	
	// we check if our plugin is active
	if(in_array('hello',unserialize($set->sinfo->active_plugins))){
	
		$value = str_replace("<body>","<body> Hello World :D <br/> This is my first plugin",$value);
		
	}

	// return the value !!
	return $value;
}



/// done now we save it in /plugins folder with the name <name>_plugin.php
// after we do that we go to Admin Panel -> Plugin manager and we should see our plugin
// we Activate it and see if it works


//// That's about it... Pretty easy huh ?
// hope you have fun and be as creative as posible with it :)
// don't forget about www.master-land.net for help

/// Regards ionutvmi@gmail.com

