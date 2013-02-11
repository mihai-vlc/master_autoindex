<?php
/**
* plugin class for master autoindex
* Copyright (C)2012 ionutvmi@gmail.com
* Licence: http://www.gnu.org/licenses/gpl-3.0.html
*/



class Plugins {
	/**
	* ! @var hook array - keeps all the hooks
	*/

	var $hooks = array();
	
	/**
	 * Run a specific hook
	 *
	 * @param string The hook name.
	 * @param mixed The value witch will be affected.
	 * @return mixed The modded value.
	*/	
	function run_hook($hook,$value = ''){
		if(isset($this->hooks[$hook])) // Fire a callback
		{
			foreach($this->hooks[$hook] as $function)
			{
				$value = call_user_func($function, $value);
			}
			return $value;
		}else{
			return $value;
		}
	}

	/**
	 * Add a hook onto which a plugin can be attached.
	 *
	 * @param string The hook name.
	 * @param string The function of this hook.
	 * @return boolean Always true.
	*/

	function add_hook($hook,$function){

		$this->hooks[$hook][] = $function;
		return true;
	}


	/**
	 * Load plugin files
	 *
	 * @return boolean Always true.
	*/
	function load($all = false){
		global $plugins,$set;
	
		$files = glob(MAI_ROOT."plugins/*_plugin.php");
		// grab active plugins list
		if(!is_array(unserialize($set->sinfo->active_plugins))) 
			$set->sinfo->active_plugins = serialize(array());

		foreach($files as $file){
			// grab plugin name
			$pname = substr(basename($file),0,-11);
			// require active plugins
			if($all == false){
				if(in_array($pname,unserialize($set->sinfo->active_plugins)))
					require_once($file);
			}else {
				if(!in_array(basename($file),array_map("basename",get_included_files())))
					require_once($file);
			}
		}
		if($all)
			$this->hooks = array(); // we don't execute on plugin manager
		return true;
	}

}