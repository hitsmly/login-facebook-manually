<?php
session_start();

$_SESSION["debug"] = 0;
if (array_key_exists( 'debug', $_REQUEST) && $_REQUEST['debug'])
	$_SESSION["debug"] = $_REQUEST['debug'];


$_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
$host =  $_SERVER['HTTP_HOST'];

$app_id = "Your_app_id";//change this
$redirect_url = "http://".$host."/callback.php"; 


$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
       . $app_id . "&redirect_uri=" . urlencode($redirect_url) . "&state="
       . $_SESSION['state'] . "&scope=public_profile,user_friends,email,publish_actions"
       . "&auth_type=rerequest"

?>
<html>
<body>
<h1>Loading ...</h1>

<script type="text/javascript">
	window.location = "<?php echo $dialog_url;?>";
</script>
</html>