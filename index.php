<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Recent Forum Activity - WordPress Forum Helper</title>
	<link rel="stylesheet" type="text/css" href="reset-fonts-grids.css">
	<link rel="stylesheet" href="style.css" type="text/css">
	<script type="text/javascript" src="resolved.js"></script>
	<meta name="robots" content="NOINDEX,NOFOLLOW">
</head>
<body>

<?php
	
	include('config.php'); // including default parameters
	include('yql-wp.php'); // including functions
	
	$profile = '';
	$content = '';
	$showform = true;
	$pagenumber = false;
	$pagequery = '';
	
	// check the url for "activity" (Recent User Activity or Threads Started)
	$activity = (isset($_GET['activity'])) ? $_GET['activity'] : 'user-replies';
	
	// set the profile
	if(trim($WordPress_profile) == '') {
	
		// no default profile is set in config.php
		
		// check if a valid profile is submitted
		if (isset($_GET['profile'])) {
			$profile = (!preg_match("/^[0-9|a-z|A-Z|-|\+|\.|_]+$/",$_GET['profile'])) ? 'invalid profile' : $_GET['profile'];
		}
		
		// if allowed, override $max_pages set in config.php 
		if ($show_pages) {
			if (isset($_GET['pages']) && !preg_match("/[^0-9]/",$_GET['pages'])) {
				if (trim($_GET['pages']) != '' || $_GET['pages'] > 0) {
				// submitted pagenumber is a number greater than zero
					
					if($max_pages == -1){
						// no limit
						$pagenumber = $_GET['pages'];
					} else {
						// limit pagenumber to $max_pages set in config.php
						$pagenumber = ($max_pages >= $_GET['pages']) ? $_GET['pages'] : $max_pages;
					}
					
					// pagequery for links Recent User Activity or Threads Started
					$pagequery = '&pages=' . $pagenumber;
					
				}
			}
		}
		
	} else {
	
		// default profile is set in config.php
		
		$profile = trim($WordPress_profile); 
		// todo - check for invalid characters in default profile? (probably overkill)
		
		$showform = false;
		
	}
	
	// if pagenumber is not set by the form, set it to 5 (default) 
	// todo - default pagenumber in config.php?
	$pagenumber = ($pagenumber) ? $pagenumber : 5;
	
	// the profile is set. get the recent activity topics from this profile.
	if ($profile == 'invalid profile') {
	
		$profile = '';
		$content = '<p>invalid profile! Try a differnt profile.</p>';
		
	} else {
	
		if($profile != ''){
		
			$content = get_profile_pages($profile, $pagenumber, $activity);
			
			// check if there are any results with this profile
			if (trim($content) == '') {
			
				$content = '<p>No results were found! Check if the <a href="http://wordpress.org/support/profile/' .  $profile . '">WordPress profile</a> is valid.</p>'; 
				$content .= '<p>Other possible causes:</p>';
				$content .= '<ul><li>wordpress.org is not online.</li>'; 
				$content .= '<li>yahoo servers are not online.</li>';
				$content .= '<li>a routing issue from your ISP</li>';
				$content .= '<li>a firewall is preventing this hack acces to the web</li></ul>';
				
			} else {
			
				// houston we have a go!!!  
				$title = ($activity == 'user-replies') ? 'Recent User Activity' : 'Recent Activity - Threads started';
				$content = '<div id="content-header"><h3 id="useractivity">'. $title . '</h3></div>' . $content;
				
			}
		}
	}
?>

<div id="doc4" class="yui-t7">
	<div id="hd">
		<h1>Recent Forum Activity</h1>
		<p>WordPress Forum Helper</p>
	</div>
	<div id="bd">
		
		<div class="yui-g">
			<?php if($showform) : ?>
			
			<form action="index.php" id="f">
				<div>
					<label for="profile">http://wordpress.org/support/profile/</label>
					<input type="text" id="profile" name="profile" value="<?php echo $profile; ?>">
					<?php if ($show_pages) : ?>
					<label for="pages">Pages</label>
					<input type="text" size="1" maxlength="4" id="pages" name="pages" value="<?php echo $pagenumber; ?>">
					<?php endif; ?>
					<input type="submit" value="submit Profile" id="submitbutton">
				</div>
			</form>
		<?php else : ?>
			<div id="noform">
				<h2>http://wordpress.org/support/profile/<?php echo $profile; ?></h2>
			</div>
		<?php endif; ?>
				
			<div class="yui-g first" id="content"> <!-- first column -->
				<?php echo $content; ?>
			</div><!-- /#content (first column) -->
	
			<div class="yui-g">
				<div class="yui-u first"><!-- second column -->
				<?php if($profile != '' && $content != '') : ?>
				<?php 
				
				if ($activity != 'user-replies') {
					// change these urls in config.php
					echo '<p><a href="' . $path . '?profile='. $profile . $pagequery . '">Recent User Activity</a></p>';
				} else {
					echo '<p><a href="' . $path . '?profile=' . $profile . '&amp;activity=user-threads'.$pagequery.'">Threads Started</a></p>';
				}
				?>
				
				<h3>WordPress.org</h3>
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
						<li><a href="http://wordpress.org/support/forum/your-wordpress">Your WordPress</a></li>
						<li><a href="http://wordpress.org/support/forum/miscellaneous">Miscellaneous</a></li>
						<li><a href="http://wordpress.org/support/forum/requests-and-feedback">Requests and Feedback</a></li>
						<li><a href="http://wordpress.org/support/forum/alphabeta">Alpha/Beta</a></li>
					</ul>
				</div><!-- /.yui-u.first  (second column) -->
				
				<div class="yui-u" ><!-- third column -->
					<p>This forum helper displays the topics of your WordPress Profile Page in order of activity. Right now it only alows you to scan five Profile Pages or less because of bandwidth concerns.</p> 
					<h3 id="download"><a class="download" href="http://dl.dropbox.com/u/1237410/recentforumactivity.zip">Download</a></h3>
					<p>This hack requires a server running php 5.</p><p> Change the hack's Settings in config.php:</p>
					<ul>
					<li>remove the "Pages" textinput</li>
					<li>change the allowed maximum pages</li>
					<li>set a default profile</li>
					<li>set the path to the hack</li>
					</ul>
					
					<h3>Inspiration</h3>
					<p>Find out why I made this hack <a href="http://wordpress.org/support/topic/feature-request-wordpress-forums-recent-activity-when-logged-in">here</a></p>
					<p>To make a simular hack like this read this article by Christian Heilmann: <a href="http://www.wait-till-i.com/2009/03/11/building-a-hack-using-yql-flickr-and-the-web-step-by-step/">Building a hack using YQL, Flickr and the web &#8211; step by step</a></p>
					<h3>Changelog</h3>
					<p>[Update 05-09-2011]<br/>Centralized all hack settings in config.php. Added the ability in the the profile form to set how many profile pages are scanned. Option to set a default WordPress Forum Profile in config.php (removes profile form if set)</p>
					<p>[Update 20-01-2011]<br/>Reformatted the list items to reflect the changes made by wordpress.org. Using localstorage to remember your profile</p>
					<p>[Update 04-08-2010]<br/>Ability to check recent activity on "Threads Started" and links to wordpress.org</p>
					<p>[Update 31-07-2010]<br/>Much faster Performance and some bug fixing</p>
				</div><!-- /.yui-u  (third column) -->
				
			</div><!-- /.yui-g -->
		</div><!-- /.yui-g -->
	</div><!-- /#bd -->
	<div id="ft">
		<p>Recent Forum Activity by keesiemeijer using <a href="http://developer.yahoo.com/yui">YUI</a> and <a href="http://developer.yahoo.com/yql/">YQL</a>.<br /> Test the YQL query in the <a href="http://y.ahoo.it/0nLVR" >YQL console</a></p>
	</div><!-- /#ft -->
</div><!-- /#doc4 /.yui-t7 -->
	
	<script type="text/javascript">
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
	
</body>
</html>