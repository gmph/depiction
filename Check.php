<?php

session_start();

if ( isset($_SESSION['twitter']['screen_name']) == FALSE ) { 
	echo "document.write('" . '<h2><br /><br /><a href="twitter-login.php" class="login">Log in with Twitter</a></h2></header><article><p class="welcome">Depiction is the simplest way to share what you are thinking right now - it is the no clutter personal status page.</p></article>' . "');";
}
else {
	echo "document.write('" . '<h2><a href="http://depiction.co/' . $_SESSION['twitter']['screen_name'] . '">Profile</a> <span class="divider">|</span> <a href="twitter-logout.php">Logout</a></h2></header><article><div class="update"><form action="Page.php" method="post"><div class="status"><!--<h3 class="title">Message</h3>--><div class="count">70</div><textarea name="message" placeholder="Enter your new status message here..." class="message"></textarea><div class="warning">Oops, you can only share up to 70 characters.</div></div><!--<h3 class="title">Colour</h3>--><div class="mood"><div class="colour" id="one"></div><div class="colour" id="two"></div><div class="colour" id="three"></div><div class="colour" id="four"></div><div class="colour" id="five"></div></div><button class="button" id="update" href="#go">Update</button></form></div></article>' . "')";
}

?>
