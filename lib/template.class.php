<?php 
/**
* plugin class for master autoindex
* Copyright (C)2012 ionutvmi@gmail.com
* Licence: http://www.gnu.org/licenses/gpl-3.0.html
*/
class Tpl {

  public $strTemplateDir = null;
  public $strBeginTag    = '{';
  public $strEndTag      = '}';
  public $strBuffer      = null;
  

	/**
	 * Grab the content of the files and run the hooks
	 *
	 * @param string The file name.
	 * @param string The hook name.
	 * @return boolean Always true.
	*/	
  public function grab($strFile,$strHook){
  global $plugins;  
		$this->strBuffer = $plugins->run_hook($strHook,file_get_contents(MAI_ROOT . MAI_TPL .$strFile));
		return true;
  }
  
	/**
	 * Replace the vars from teplate with the specific value
	 *
	 * @param string The var name.
	 * @param string The var value.
	 * @return boolean Always true.
	*/	  
  public function assign( $strVar, $strValue ) {
    $this->strBuffer   = str_replace($this->strBeginTag . '$' . $strVar  . $this->strEndTag , $strValue, $this->strBuffer);
	return true;
  }
  
  
  // display the final result
  public function display() {
	echo $this->strBuffer;
  }
} 
?>