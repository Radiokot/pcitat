<?php

include_once "../php/UserManager.php";
include_once "../php/.credentials.php";
include_once "./.config.php";

include_once "../php/lib/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

error_reporting(E_ALL & ~E_DEPRECATED);

$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
$request_key = isset($_REQUEST['request_key']) ? $_REQUEST['request_key'] : null;

$protocol = (isset($_SERVER['HTTPS']) || FORCE_HTTPS) ? "https:" : "http:";

if (!isset($_REQUEST['oauth_token']) && !isset($_REQUEST['oauth_verifier']) && !isset($_REQUEST['denied'])) {
	// Need auth.
	$self_url = $protocol."//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $self_url));

	$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

	header("Location: ".$url);
} else {
	// Process auth result.
	if (!isset($_REQUEST['denied']) && $email != null && $request_key != null) {
		$user = UserManager::getByEmail($email);
		$key = hash("sha256", $user["email"].$user["password"]);
		$oauth_token = $_REQUEST['oauth_token'];
		$oauth_verifier = $_REQUEST['oauth_verifier'];

		if ($request_key == hash("sha256", $key)) {
			$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, 
				$oauth_token, $oauth_verifier);
			$access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier]);

			UserManager::setTwitterName($user["id"], $access_token['screen_name']);
		}
	}

	$redirect_url = $protocol.TWITTER_REDIRECT_URL;
	header("Location: ".$redirect_url);
}

?>