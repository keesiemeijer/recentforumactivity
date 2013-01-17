<?php 
	
	/**
	 * set the url for this app (where the app lives on your server)
	 *
	 * used as the action attribute in the profile form
	 * <form action="<?php $url; ?>">
	 *
	 * (examples)
	 * $url = 'http://www.stoerke.be/recentforumactivity/';
	 * default = '';
	 *
	 * (string) ablolute url or empty string;
	 */
	
	$url = '';
	
	
	/**
	 * set a default (valid) WordPress forum profile 
	 *
	 * if set the form to submit a profile is removed from this app
	 * default = $WordPress_profile = ''; (no default WordPress profile)
	 *
	 * (string) profile name
	 */
	 
	$WordPress_profile = ''; 
	

	/**
	 * maximum profile pages to be scanned 
	 * default is 5 pages, but can be changed with the profile form if $show_pages (below) is set to true.
	 *
	 * (int)
	 */
	 
	$max_pages = 5;
	
	
	/**
	 * show or hide the pagenumber dropdown in the profile form
	 *
	 * important: because the form is saved in Javascript localstorage
	 *            changes to this variable will only take effect (in your browser) after submitting the form once  
	 *
	 * (boolean) true or false 
	 */
	 
	$show_pages = true;
	
	
	/**
	 * Hides the second column if set to false: $show_second_column = false;
	 * (boolean) true or false
	 */	
	 
	$show_second_column = true;
	
	
	/**
	 * Hides the third column if set to false: $show_third_column = false;
	 * (boolean) true or false
	 */	
	 	
	$show_third_column = true;

