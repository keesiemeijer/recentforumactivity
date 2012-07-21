<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Recent Forum Activity</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<meta name="robots" content="NOINDEX,NOFOLLOW">
		
		<!-- Le styles -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="assets/css/recentforumactivity.min.css" rel="stylesheet">
		
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.js"></script>
		<![endif]-->
		<script type="text/javascript" src="assets/js/resolved.min.js"></script>
		
		<!-- Le fav and touch icons -->
		<link rel="shortcut icon" href="assets/ico/favicon.ico">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="assets/ico/apple-touch-icon-57-precomposed.png">
		
	</head>
	
	<body>
	<?php
	
	include('config.php'); // including default parameters
	include('yql-wp.php'); // including functions
	
	$profile = '';
	$content = '';
	$error = '</h3><div class="alert alert-info">Please submit a WordPress dot org profile in the form above.</div>';
	$showform = true;
	$pagenumber = false;
	$pagequery = '';
	
	// check the url for "activity" (Recent User Activity or Threads Started)
	$activity = (isset($_GET['activity'])) ? $_GET['activity'] : 'user-replies';
	$title = ($activity == 'user-replies') ? 'Recent User Activity' : 'Recent Activity - Threads started';
	
	// set the profile
	if(trim($WordPress_profile) == '') {
	
		// no default profile is set in config.php
		
		// check if a valid profile is submitted
		if (isset($_GET['profile'])) {
			$profile = (!preg_match("/^[0-9|a-z|A-Z|\-|\+|\.|_]+$/",$_GET['profile'])) ? 'invalid profile' : $_GET['profile'];
		}		
		
	} else {
	
		// default profile is set in config.php
		
		$profile = trim($WordPress_profile); 
		// todo - check for invalid characters in default profile? (probably overkill)
		
		$showform = false;
		
	}
	
	// if allowed, override $max_pages set in config.php 
	if ($show_pages) {
		if (isset($_GET['pages']) && !preg_match("/[^0-9]/",$_GET['pages'])) {
			if (trim($_GET['pages']) != '' || (int) $_GET['pages'] > 0) {
				// submitted pagenumber is a number greater than zero
				
				// limit pagenumber to $max_pages set in config.php
				$pagenumber = ($max_pages >= $_GET['pages']) ? $_GET['pages'] : $max_pages;
				
				// pagequery for links Recent User Activity or Threads Started
				$pagequery = '&amp;pages=' . $pagenumber;
				
			} 
		} else {
			$pagenumber = $max_pages;
		}
	}
	
	// set $pagenumber to $max_pages in config.php if pagenumber is not altered with the form 
	$pagenumber = ($pagenumber) ? $pagenumber : $max_pages;
	
	// the profile is set. get the recent activity topics from this profile.
	if ($profile == 'invalid profile') {
	
		$profile = '';
		$error = '<div class="alert alert-error">invalid profile! Submit a different profile.</div>';
		
	} else {
	
		if($profile != ''){
		
			$content = get_profile_pages($profile, $pagenumber, $activity);
			
			// check if there are any results with this profile
			if (trim($content) == '') {
				$error = '<div class="alert alert-error">';
				$error .= '<p>No results were found!</p>';
				$error .= '<p>Check if the <a href="http://wordpress.org/support/profile/' .  $profile . '">WordPress profile</a> is valid.</p>';
				$error .= '<p>Other possible causes:</p>';
				$error .= '<ul><li>wordpress.org is not online.</li>'; 
				$error .= '<li>yahoo servers are not online.</li>';
				$error .= '<li>a routing issue from your ISP</li>';
				$error .= '<li>a firewall is preventing this hack acces to the web</li></ul>';
				$error .= '</div>';
			} 
		}
	}
	
?>
		
		
		<div class="container-fluid">
		
		
			<h1>Recent Forum Activity</h1>
			<p class="tagline">display the topics of your WordPress [dot] org Profile Pages in order of activity</p>
				
			<?php if($showform) : ?>
			
			<form action="<?php echo $url; ?>" id="f" class="form-inline well">
					
				<fieldset>
					
					<label for="profile">http://wordpress.org/support/profile/
					<input type="text" class="input-xlarge" id="profile" name="profile" value="<?php echo $profile; ?>"></label>
					
				<?php if ($show_pages) : ?>
									
					<label for="pages">pages:
					<select name="pages" id="pages" class="span1">
					<?php 
						
								for ($i = 1; $i <= (int) $max_pages; $i++) {
									echo '<option value="'.$i.'"';
									if($pagenumber == $i) {
										echo ' selected="selected"';
									}
									echo '>'.$i.'</option>';
								}
						?>
						
					</select>
					</label>
				<?php endif; // end if $show_pages ?>
					
					<input type="submit" value="submit Profile" id="submitbutton" class="btn ">
				</fieldset>	
			</form>
			<?php else : ?>
			<div class="well">
				<h3>http://wordpress.org/support/profile/<?php echo $profile; ?></h3>
			</div>
			<?php endif; // end if $showform ?>
				
			
			
			<!-- row of columns -->
			<div class="row-fluid">
				<?php 
				// no columns are hidden (default)
				$span = 'span6';
				$offset = '';
				
				// One column is hidden
				if(!$show_second_column || !$show_third_column){
					$span = 'span8';
				}
				// both columns are hidden
				if(!$show_second_column && !$show_third_column){
					$span = 'span12';
			  } 
			  ?>
				<div class="<?php echo $span; ?>"><!-- first column -->
				<?php 
					if($content == ''){
						echo '<h3>' . $title . '</h3>' .$error;
					} else {
						echo '<h3 id="useractivity">' . $title . '</h3>' . $content;
					}
				?>
				</div><!-- /first column -->
				<?php if($show_second_column) : ?>
				<?php $span = ($span == 'span8') ? 'span4' :  'span3' ?>
				<div class="<?php echo $span; ?>"><!-- second column -->
					
						<?php if($content != '') : ?>
						<h3>WordPress.org</h3>
						<?php 
									if ($activity != 'user-replies') {
										// change these urls in config.php
										echo '<p><a href="' . $url . '?profile='. $profile . $pagequery . '">Recent User Activity</a></p>';
									} else {
										echo '<p><a href="' . $url . '?profile=' . $profile . '&amp;activity=user-threads'.$pagequery.'">Threads Started</a></p>';
									}
						?>
						
						
						<p><a href="http://wordpress.org/support/profile/<?php echo $profile; ?>">Your Profile Page</a></p>
						<?php endif; ?>
						
						<h3>Forums</h3>
						<ul>
							<li><a href="http://wordpress.org/support/forum/installation">Installation</a></li>
							<li><a href="http://wordpress.org/support/forum/how-to-and-troubleshooting">How-To and Troubleshooting</a></li>
							<li><a href="http://wordpress.org/support/forum/themes-and-templates">Themes and Templates</a></li>
							<li><a href="http://wordpress.org/support/forum/plugins-and-hacks">Plugins and Hacks</a></li>
							<li><a href="http://wordpress.org/support/forum/hacks">Hacks</a></li>
							<li><a href="http://wordpress.org/support/forum/wp-advanced">WP-Advanced</a></li>
							<li><a href="http://wordpress.org/support/forum/multisite">Multisite</a></li>
							<li><a href="http://wordpress.org/support/forum/localhost-installs">Localhost Installs</a></li>
							<li><a href="http://wordpress.org/support/forum/your-wordpress">Your WordPress</a></li>
							<li><a href="http://wordpress.org/support/forum/miscellaneous">Miscellaneous</a></li>
							<li><a href="http://wordpress.org/support/forum/requests-and-feedback">Requests and Feedback</a></li>
							<li><a href="http://wordpress.org/support/forum/alphabeta">Alpha/Beta</a></li>
							<li><a href="http://wordpress.org/support/forum/meetups">Meetups</a></li>
						</ul>
						<ul>
							<li class="view"><a href="http://wordpress.org/support/view/all-topics">All Topics</a></li>
							<li class="view"><a href="http://wordpress.org/support/view/no-replies">No Replies</a></li>
							<li class="view"><a href="http://wordpress.org/support/view/plugin">Plugin Support</a></li>
							<li class="view"><a href="http://wordpress.org/support/view/support-forum-no">Not Resolved</a></li>
							<li class="view"><a href="http://wordpress.org/support/view/untagged">No Tags</a></li>
						</ul>
					</div> <!-- /second column -->
				<?php endif; ?>
				<?php if($show_third_column) : ?>
				<?php $span = ($span == 'span8') ? 'span4' :	'span3' ?>
					<div class="<?php echo $span; ?>"><!-- third column -->
						
						<p>Download this app to run it on your own server or localhost.</p>
						<p><a class="btn btn-success" href="http://dl.dropbox.com/u/1237410/recentforumactivity.zip">
							<i class="icon-download icon-white"></i>
							Download</a>
						</p>
						<p>Requires a server running php 5.</p><p>Change the settings of this app in the config.php file:</p>
						<ul>
							<li>set the url to the profile form</li>
							<li>set a default profile</li>
							<li>remove the "Pages" dropdown</li>
							<li>set the allowed maximum pages to scan</li>
							<li>hide the second or third column</li>							
						</ul>
						
						<h3>Inspiration</h3>
						<p>After <a href="http://wordpress.org/support/topic/feature-request-wordpress-forums-recent-activity-when-logged-in">asking around</a> on the forums I found out this feature was on their todo list for a long time and would not be implemented soon. So I made it myself.</p>
						<p>Read this awesome article by Christian Heilmann on how to make a simular app like this: <a href="http://www.wait-till-i.com/2009/03/11/building-a-hack-using-yql-flickr-and-the-web-step-by-step/">Building a hack using YQL, Flickr and the web &#8211; step by step</a></p>
						<h3>Changelog</h3>
						<p>[Update 17-07-2012]<br/>New Responsive layout with Bootstrap. Added ability to easily hide the second and third column</p>
						<p>[Update 05-09-2011]<br/>Centralized all app settings in config.php. New page dropdown in profile form. Option to set a default WordPress Forum Profile in config.php (removes profile form if set)</p>
						<p>[Update 20-01-2011]<br/>Reformatted the list items to reflect the changes made by wordpress.org. Using localstorage to remember your profile</p>
						<p>[Update 04-08-2010]<br/>Ability to check recent activity on "Threads Started" and links to wordpress.org</p>
						<p>[Update 31-07-2010]<br/>Much faster Performance and some bug fixing</p>
					</div><!-- /third column -->
				<?php endif; ?>
			</div><!-- /row-fluid -->
			
			<hr>

			<footer>
				<p>Recent Forum Activity by keesiemeijer using <a href="http://developer.yahoo.com/yui">YUI</a> and <a href="http://developer.yahoo.com/yql/">YQL</a>.<br /> Test the YQL query in the <a href="http://y.ahoo.it/0nLVR" >YQL console</a></p>
			</footer>
		
		</div> <!-- /container -->
	<?php if($showform) : ?>
		<script>
	// in stead of cookies I use local storage in modern browsers to cache your profile 
	// next time you visit this site your profile will allready be inserted into the form
	
	// test for localStorage support
	if(('localStorage' in window) && window['localStorage'] !== null){
	var f = document.getElementById('f');
	
	<?php if(isset($_GET['profile'])){?>
	localStorage.setItem('my_WP_profile',f.innerHTML);
	document.getElementById('submitbutton').focus();
	<?php } else { ?>
						if('my_WP_profile' in localStorage){
							f.innerHTML = localStorage.getItem('my_WP_profile');
							document.getElementById('submitbutton').focus();
						} else {
							var profile = document.getElementById('profile');
							profile.focus();
						}
						
		<?php } ?>
	}
	</script>
	<?php endif; ?>
	</body>
</html>
