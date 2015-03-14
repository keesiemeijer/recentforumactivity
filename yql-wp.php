<?php
/**
 * Returns Html list with topics from the Wordpress Forums wich have recent activity.
 *
 * this function requires PHP 5, curl and uses YQL ( http://developer.yahoo.com/yql/ )
 *
 * @param string  $profile  WordPress dot org profile
 * @param integer $pages    How many profile pages to scrape for recent activity.
 * @param string  $activity Get recent activity topics from user replies or threads started.
 * @return string           Empty string or html list with topics from the Wordpress Forums wich have recent activity.
 */
function get_profile_pages( $profile = '', $pages = 3, $activity = 'user-replies' ) {

	$html = '';
	$url = $results = array();

	$profile = trim( (string) $profile );
	if ( '' === $profile  || !function_exists( 'curl_version' ) || version_compare( phpversion(), "5", "<" ) ) {
		return $html;
	}

	$pages = abs( intval( $pages ) );
	$pages = ( $pages ) ? $pages : 3;

	$activity = ( 'user-replies' === $activity ) ? 'user-replies' :'user-threads';

	// populate an array of profile urls to use in the YQL query
	for ( $i = 1; $i <= $pages; $i++ ) {
		$url[] = "http://wordpress.org/support/profile/{$profile}/page/{$i}";
	}

	/**
	 * the YQL query used in this app with my WordPress profile.
	 *
	 * select * from html where url in ('http://wordpress.org/support/profile/keesiemeijer') and xpath="//div[@id='user-replies']/ol/li"
	 *
	 * test the YQL query with my WordPress profile at: http://y.ahoo.it/0nLVR
	 */

	$profileUrl = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%20in%20('" . implode( "','", $url ) . "')%20and%20xpath%3D%22%2F%2Fdiv%5B%40id%3D\'" . $activity . "\'%5D%2Fol%2Fli%22%0A&format=json";

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, $profileUrl );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$output = curl_exec( $ch );
	curl_close( $ch );

	$allResults = json_decode( $output, true );

	// check if there are any results from the query
	if ( isset( $allResults['query']['count'] ) && ( (int) $allResults['query']['count'] > 0 ) ) {

		$results = ( isset( $allResults['query']['results']['li'] ) ) ? $allResults['query']['results']['li'] : array();

		// if there is only one topic make it an array (format is different than multiple topics)
		if ( $allResults['query']['count'] == 1 ) {
			$results = array( $results );
		}

	}

	if ( !empty( $results ) ) {

		$recentResults = array();
		$i = 0;
		// give the list items in the $results array a time variable that we can later use to sort the results
		foreach ( $results as $reply ) {

			$topic_content = $reply['span']['content'];

			// (Most recent reply: 3 minutes ago)
			// check if the word "Most" is in the list item's content
			$most = strpos( $topic_content, 'Most' );
			if ( $most !== false ) {

				// get the number from the list item's content
				$topic_time = preg_replace( '/[^\d]+/', '', preg_replace( '/by.*/', '', $topic_content ) );

				// make the time variable
				++$i; // counter used to give each topic a unique time number

				$time_in_seconds = array(
					'minute' => 60, 'hour' => 3600, 'day' => 86400, 'week' => 604800,
					'month' => 2419200, 'year' => 29030400
				);

				foreach ( $time_in_seconds as $type => $sec ) {
					if ( strpos( $topic_content, $type ) !== false ) {
						$topic_time = ( $topic_time * $sec ) + $i;
					}
				}

				// give the reply a time variable for sorting
				$reply['topic_time'] = $topic_time;

				// make the final array of topics that have recent activity and a time variable
				$recentResults[] = $reply;
			}
		}

		if ( !empty( $recentResults ) ) {

			// sort the topics on time
			usort( $recentResults, "cmp" );

			// make the output for this function (unordered list)
			$html .='<ul class="unstyled" id="content">'. "\n";
			foreach ( $recentResults as $reply ) {
				$resolved = '';
				$class = '';
				if ( substr( trim( $reply['content'] ), 0, 10 ) == '[resolved]' ) {
					$reply['content'] = substr( trim( $reply['content'] ), 10 );
					$resolved = '[resolved]';
					$class = ' class="resolved"';
				}
				$html .= '<li' . $class . '>'. "\n";
				$html .= '<p><span class="topic">' . $resolved . ' <a href="'. $reply['a']['href'] . '" >' . $reply['a']['content'] . '</a></span>' . "\n";
				$html .= $reply['content'];

				$html .= '<span class="freshness">';
				$spancontent= preg_replace( '/Most recent reply: (.*?) ago/', 'Most recent reply: <span class="badge">\1 ago</span>', $reply['span']['content'] );
				$html .= $spancontent . '</span></p>' . "\n" . '</li>' . "\n\n";
			}
			$html .= '</ul>' . "\n";
		}

	} // end if (!empty($results))

	return $html;
}


/**
 * callback function for usort: cmp
 */
function cmp( $a, $b ) {

	if ( $a['topic_time'] == $b['topic_time'] ) {
		return 0;
	}

	return ( $a['topic_time'] < $b['topic_time'] ) ? -1 : 1;
}