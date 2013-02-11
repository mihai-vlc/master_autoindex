<?php

/*
* Mihai Ionut Vilcu (ionutvmi@gmail.com)
* Feb 2013
* master autoindex
* master-land.net
* array pagination class
*/



class array_pagination {
    var $page = 1; // Current Page
    var $perPage = 10; // Items on each page, defaulted to 10

	
	// slice the array
	function generate($array, $perPage = 10) {
	
      // Assign the items per page variable
      if (!empty($perPage))
        $this->perPage = $perPage;
      
      // Assign the page variable
      if (!empty($_GET['page'])) {
        $this->page = $_GET['page']; // using the get method
      } else {
        $this->page = 1; // if we don't have a page number then assume we are on the first page
      }
      
      // Take the length of the array
      $this->length = count($array);
      
      // Get the number of pages
      $this->pages = ceil($this->length / $this->perPage);
      
	  if($this->page > $this->pages) $this->page=$this->pages;
	  
      // Calculate the starting point 
      $this->start  = ceil(($this->page - 1) * $this->perPage);
      
      // Return the part of the array we have requested
      return array_slice($array, $this->start, $this->perPage);
    }
	

}