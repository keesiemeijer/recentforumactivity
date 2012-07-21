<?php 

	// this script requires PHP 5 and uses YQL ( http://developer.yahoo.com/yql/ )
	// echo 'Current PHP version: ' . phpversion();
	
	/** 
	 *	a detailed explanation of the YQL code, written by Christian Heilmann, can be found here: 
	 *	http://www.wait-till-i.com/2009/03/11/building-a-hack-using-yql-flickr-and-the-web-step-by-step/
	 */
	 
	
	/**
	 * function: get_profile_pages
	 *
	 * returns: an unordered list with topics from the Wordpress Forums wich have recent activity
	 * returns: an empty string if no results were found
	 */
	function get_profile_pages($profile = '', $pages = 3, $activity = 'user-replies') {
	
		$html = '';
		$url = array();
		
		// populate an array of profile urls to use in the YQL query
		for ($i = 1; $i <= $pages; $i++) { 
			$url[] = 'http://wordpress.org/support/profile/'.$profile.'/page/'.$i;
		}
				
		// the YQL query used in this hack with my WordPress profile:
		// select * from html where url in ('http://wordpress.org/support/profile/keesiemeijer') and xpath="//div[@id='user-replies']/ol/li"
		// test the YQL query with my WordPress profile here: http://y.ahoo.it/0nLVR
		
		$profileUrl =	"http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%20in%20('".implode("','",$url)."')%20and%20xpath%3D%22%2F%2Fdiv%5B%40id%3D\'".$activity."\'%5D%2Fol%2Fli%22%0A&format=json";
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $profileUrl); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$output = curl_exec($ch); 
		curl_close($ch);
		$allResults = json_decode($output, true);
		
		$results = array();
		
		// check if there are any results from the query
		if ($allResults['query']['count'] > 0) {
		
			// if there is only one topic make it an array (wordpress.org formats it different than multiple topics)
			if ($allResults['query']['count'] == 1) { 
					$allResults['query']['results']['li'] = array($allResults['query']['results']['li']);
			}
			
			// sanatize the $allResults array (wordpress.org formats resolved and unresolved topics differently)
			foreach ($allResults['query']['results']['li'] as $reply) { 
			
				$link = ($reply['a']) ? $reply['a'] : '';
				$p = $reply['p'];
				if ($link != '') {
					$p = array('a' => $link)+$p;
				} 
				$results[] = $p;
				
			}
		}
		
		if (!empty($results)){

			$recentResults = array(); 
			$i = 0;
			// give the list items in the $results array a time variable that we can later use to sort the results 
			foreach ($results as $reply) { 
				$topic_content = $reply['span']['content']; // (Most recent reply: 3 minutes ago)
				
				// check if the word "Most" is in the list item's content
				$most = strpos($topic_content, 'Most'); 
				if ($most !== false ) { 
					
					// get the number from the list item's content 
					$topic_time = preg_replace('/[^\d]+/','',$topic_content); 
					
					// make the time variable
					++$i; // counter used to give each topic a unique time number
					$minute = strpos($topic_content, 'minute'); 
					if ($minute !== false ) { $topic_time = $topic_time *60+$i; }
					$hour = strpos($topic_content, 'hour');
					if ($hour !== false ) { $topic_time = $topic_time *60*60+$i;}
					$day = strpos($topic_content, 'day'); 
					if ($day !== false ) { $topic_time = $topic_time *60*60*24+$i;}
					$week = strpos($topic_content, 'week'); 
					if ($week !== false ) { $topic_time = $topic_time *60*60*24*7+$i; }
					$month = strpos($topic_content, 'month'); 
					if ($month !== false ) { $topic_time = $topic_time *60*60*24*7*4+$i; }
					$year = strpos($topic_content, 'year'); 
					if ($year !== false ) { $topic_time = $topic_time *60*60*24*7*4*12+$i; }
					
					// give the reply a time variable for sorting
					$reply['topic_time'] = $topic_time;
					
					// make the final array of topics that have recent activity and a time variable
					$recentResults[] = $reply;
				}
			}
			
			// sort the topics on time
			usort($recentResults, "cmp");
			
			// make the output for this function (unordered list)
			// todo: check $recentResults
			
			$html .='<ul>'. "\n";
			foreach ($recentResults as $reply) {
				$resolved = '';
				$class = '';
				if (substr($reply['content'], 0, 10) == '[resolved]') {
					$reply['content'] = substr($reply['content'], 10);
					$resolved = '[resolved]';
					$class = ' class="resolved"';
				}
				$html .= '<li' . $class . '>'. "\n";
				$html .= $resolved . ' <a href="'. $reply['a']['href'] . '" >' . $reply['a']['content'] . '</a>' . "\n";
				$html .= '<p>' . $reply['content'];
				$html .= '<span class="freshness">';
				$html .= $reply['span']['content'] . '</span></p>' . "\n" . '</li>' . "\n\n";
			}
			$html .= '</ul>' . "\n";
			
		} // end if (!empty($results)) {}
		
		return $html; 
	}
	
	
	/**
	 * callback function for usort: cmp
	 *
	 * orders $recentResults array() by time
	 */
	 
	function cmp($a, $b) {
		
		if ($a['topic_time'] == $b['topic_time']) {
			return 0;
		}
		
		return ($a['topic_time'] < $b['topic_time']) ? -1 : 1;
		
	}
	
?>