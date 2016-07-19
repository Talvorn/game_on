<?php

$con = mysqli_connect('localhost', 'root', '', 'game_on') or false;

// escapes special characters from user inputs
function sanitise($input){
	global $con;

	$input = mysqli_real_escape_string($con, $input);
	$input = stripslashes($input);
	$input = htmlspecialchars($input);

	return $input;
}

// performs database queries and returns results
// returns false for query errors or zero row results
function query($sql){
	global $con;	
	$query = mysqli_query($con, $sql);

	if(!$query || @mysqli_num_rows($query) === 0){
		return false;
	}
	else{
		return $query;
	}
}

// checks database to determine existence of a username
function userExists($username){
	sanitise($username);
	$userExists = query('SELECT * FROM users WHERE username = "'.$username.'"');
	if($userExists){
		return true;
	}
	else{
		return false;
	}

}

// performs user authentication
function userAuth($username, $password){

	if(userExists($username)){
		$dbPass = $userDetails['user_password'];

		if(password_verify($password, $dbPass)){
			$userDetails = query('SELECT * FROM users WHERE username = "'.$username.'"');
			$userDetails = mysqli_fetch_assoc($userDetails);
			return json_encode($userDetails);
		}
	}
	else{
		return false;
	}

}

// return details for a given platform
function platformDetails($platformID){
	$platformInfo = 'SELECT * FROM platforms
					 WHERE platform_id = '.$platformID;
	$platformInfo = query($platformInfo);
	if(!$platformInfo){
		return false;
	}
	else{
		return json_encode(mysqli_fetch_assoc($platformInfo));
	}
}

// lists all game platforms
function listPlatforms(){
	$getPlatforms = 'SELECT * FROM platforms';
	$getPlatforms = query($getPlatforms);
	$i=0;

	if(!$getPlatforms){
		return 'false';
	}
	else{
		$platformList = array();
		while($row = mysqli_fetch_assoc($getPlatforms)){
			$platformList[$i] = $row;
			$i++;
		}
		return json_encode($platformList);
	}

}

// lists all reviews containing a searched string
function searchResults($search){
	$searchQuery = 'SELECT * FROM reviews
					INNER JOIN users
					ON reviews.user_id = users.user_id
					WHERE review_title LIKE "%'.$search.'%"
					OR review_content LIKE "%'.$search.'%"';
	$searchQuery = query($searchQuery);
	$i = 0;

	if(!$searchQuery){
		return false;
	}
	else{
		$searchResults = array();
		while($row = mysqli_fetch_assoc($searchQuery)){
			$searchResults[$i] = $row;
			$i++;
		}
		return json_encode($searchResults);
	}
}

// returns an array of most recent review objects
// default array length is 5
function latestReviews($numReviews = 5){
	$reviewQuery = 'SELECT * FROM reviews
					INNER JOIN users
					ON reviews.user_id = users.user_id
					ORDER BY review_time DESC
					LIMIT '.$numReviews;
	$reviewQuery = query($reviewQuery);
	$i=0;

	if(!$reviewQuery){
		return false;
	}
	else{
		$reviewList = array();
		while($row = mysqli_fetch_assoc($reviewQuery)){
			$reviewList[$i] = $row;
			$i++;
		}
		return json_encode($reviewList);
	}

}

// returns an array of most recent review objects for a given platform
// default array length is 5 
function latestPlatformReviews($platformID, $numReviews = 5){
	$reviewQuery = 'SELECT * FROM reviews
					INNER JOIN users
					ON reviews.user_id = users.user_id
					WHERE platform_id = '.$platformID.'
					ORDER BY review_time DESC
					LIMIT '.$numReviews;
	$reviewQuery = query($reviewQuery);
	$i=0;

	if(!$reviewQuery){
		return false;
	}
	else{
		$reviewList = array();
		while($row = mysqli_fetch_assoc($reviewQuery)){
			$reviewList[$i] = $row;
			$i++;
		}
		return json_encode($reviewList);
	}
}

// returns an array of all review objects for a given platform
function allPlatformReviews($platformID){
	$reviewQuery = 'SELECT * FROM reviews
					INNER JOIN users
					ON reviews.user_id = users.user_id
					WHERE platform_id = '.$platformID.'
					ORDER BY review_time DESC';
	$reviewQuery = query($reviewQuery);
	$i=0;

	if(!$reviewQuery){
		return false;
	}
	else{
		$reviewList = array();
		while($row = mysqli_fetch_assoc($reviewQuery)){
			$reviewList[$i] = $row;
			$i++;
		}
		return json_encode($reviewList);
	}

}

// retrieve a review's information from the database
function reviewDetails($reviewID){
	$reviewDetails = 'SELECT * FROM reviews
					  INNER JOIN platforms
					  ON reviews.platform_id = platforms.platform_id
					  INNER JOIN users
					  ON reviews.user_id = users.user_id
					  WHERE review_id = '.$reviewID;

	$reviewDetails = query($reviewDetails);

	if(!$reviewDetails){
		return false;
	}
	else{
		return json_encode(mysqli_fetch_assoc($reviewDetails));
	}
}


// return array of comment objects for a given review
function reviewComments($reviewID){
	$reviewComments = 'SELECT * FROM comments
					   INNER JOIN users
					   ON comments.user_id = users.user_id
					   WHERE review_id = '.$reviewID;
	$reviewComments = query($reviewComments);
	$i = 0;

	if(!$reviewComments){
		return false;
	}
	else{
		$commentList = array();
		while($row = mysqli_fetch_assoc($reviewComments)){
			$commentList[$i] = $row;
			$i++;
		}
		return json_encode($commentList);
	}

}

// retrieves a user's information from the database
function userInfo($userID){
	$userInfo = 'SELECT * FROM users
				 WHERE user_id = '.$userID;
	$userInfo = query($userInfo);
	if(!$userInfo){
		return false;
	}
	else{
		return json_encode(mysqli_fetch_assoc($userInfo));
	}
}

?>