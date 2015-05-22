<?php 
	session_start();
	$host =  $_SERVER['HTTP_HOST'];
   	$app_id = "your_app_id"; //change this
   	$app_secret = "your_app_secret"; //change this

   	$redirect_url = "http://".$host."/callback.php";
   
	$code = $_REQUEST["code"];

	if(empty($code)) 
	{
		header( 'Location: http://'.$hosts.'/fblogin.php' ) ; //change this
		exit(0);
	}

	$access_token_details = getAccessTokenDetails($app_id,$app_secret,$redirect_url,$code);
	if($access_token_details == null)
	{
		echo "Unable to get Access Token";
		exit(0);
	}   

	if($_SESSION['state'] == null || ($_SESSION['state'] != $_REQUEST['state'])) 
	{
		die("May be CSRF attack");
	}
	 
   	$_SESSION['access_token'] = $access_token_details['access_token']; //save token is session 
   
   	if (array_key_exists( 'debug', $_SESSION) && $_SESSION['debug'])
   	{
   		$user = getUserDetails($access_token_details['access_token']);
   		//permission($_SESSION['access_token'], $user->id);
   		postfeed ($_SESSION['access_token'], $user->id);
   	}else{
   		header( 'Location: http://'.$host.'/token.php?token='.$_SESSION['access_token'] ) ; //change this
   	}
   
   /*if($user)
   {
		echo "Facebook OAuth is OK<br>";
		echo "<h3>User Details</h3><br>";
		echo "<b>ID: </b>".$user->id."<br>";
		echo "<b>Name: </b>".$user->name."<br>";
		echo "<b>First Name: </b>".$user->first_name."<br>";
		echo "<b>Last Name: </b>".$user->last_name."<br>";
		echo "<b>Username: </b>".$user->username."<br>";
		echo "<b>Profile Link: </b>".$user->link."<br>";
		echo "<b>email: </b>".$user->email."<br>";
		
   }*/
	
	
function getAccessTokenDetails($app_id,$app_secret,$redirect_url,$code)
{

	$token_url = "https://graph.facebook.com/oauth/access_token?"
		  . "client_id=" . $app_id . "&redirect_uri=" . urlencode($redirect_url)
		  . "&client_secret=" . $app_secret . "&code=" . $code;

	$arrContextOptions=array(
	    "ssl"=>array(
	        "verify_peer"=>false,
	        "verify_peer_name"=>false,
	    ),
	);  

	$response = file_get_contents($token_url, false, stream_context_create($arrContextOptions));
	$params = null;
	parse_str($response, $params);
	
	print_r($params);
	return $params;
}

function postfeed($access_token, $user_id){
	$url = "https://graph.facebook.com/".$user_id."/feed";
		$attachment =  array(
		'access_token' => $access_token,
		'message' => "genius",
		'name' => "Your app name",
		'link' => "Your app domain",
		'description' => "Your app description",
		'picture'=> "http://domain.name/your_image.png",
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //to suppress the curl output 
	$result = curl_exec($ch);
	curl_close ($ch);

	print_r($result);
	return $result;
}

function getUserDetails($access_token)
{
	$graph_url = "https://graph.facebook.com/me?access_token=". $access_token;
	$arrContextOptions=array(
	    "ssl"=>array(
	        "verify_peer"=>false,
	        "verify_peer_name"=>false,
	    ),
	);  

	$response = file_get_contents($graph_url, false, stream_context_create($arrContextOptions));
	$params = (json_decode($response));
	echo '<br><br> getUserDetails:';
	print_r($params); 
	return $params;
}


 ?>