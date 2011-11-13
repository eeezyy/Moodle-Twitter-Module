<?php
	// account login data
	$usr = $_POST['user'];
	$pwd = $_POST['pwd'];
	$account = $_POST['account'];
	
	// when owner of the twitter channel will follow himself, show error message
	if($usr==$account){
		echo "<div align='center' style='margin-top:100px'>".get_string('twitterfollowselferror','twitter')."</div>";
	} else {
		require_once('./api/twitter.php');
		// check if user is following already
		$friends = twitter_call($usr, $pwd, "http://twitter.com/friends/ids.xml");
		$alreadyFollowing = false;
		foreach($friends->id as $id){
			$user_info = twitter_call($usr, $pwd,"http://api.twitter.com/1/users/show.xml?user_id=".$id);
			if ($user_info->screen_name == $account){
				$alreadyFollowing = true;
			}
		}
		// if user is not in the following list, add him
		if(!$alreadyFollowing){
			$result = twitter_call($usr, $pwd, "http://twitter.com/friendships/create/".$account.".xml", "POST");
			if(isset($result->error)) {
				echo "<div align='center' style='margin-top:100px; color:red'>".get_string('twitterfollowerror','twitter')."<br /><br />";
				echo "<a href='{$_SERVER['PHP_SELF']}?id={$_GET['id']}'>".get_string('twitterfollowbacklink','twitter')."</a>";
				echo "</div>";
			} else {
				echo "<div align='center' style='margin-top:100px'>".get_string('twitterfollowsuccess','twitter')."</div>";
			}
		} else {
			// else show error message
			echo "<div align='center' style='margin-top:100px'>".get_string('twitterfollowalready','twitter')."</div>";
		}
	}
?>