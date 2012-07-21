<?php 

	/**
	 * url to this hack on your own site.
	 *
	 * (example)
	 * $path = http://www.yoursite.com/recentforumactivity/
	 */

	
	$path = 'http://www.stoerke.be/recentforumactivity/';
	
	/**
	 * set a default (valid) WordPress forum profile 
	 *
	 * if set the form to submit a profile is removed from this hack
	 * default = empty string ($WordPress_profile = '';) (no default WordPress profile)
	 */
	$WordPress_profile = ''; 
	
	/**
	 * show or hide the pagenumber text input in the profile form
	 * (boolean) true or false
	 */
	$show_pages = true;

	/**
	 * maximum of profile pages to be scanned 
	 * use -1 to allow any number of pages to be scanned (on my setup 147 pages was the maximum)
	 * default is 4 pages (index.php), but can be changed with the profile form if $show_pages is set to true.
	 */
	$max_pages = 5;

?>