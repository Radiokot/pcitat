<?php

include_once "../php/UserManager.php";
include_once "../php/.credentials.php";
include_once "./.config.php";
include_once dirname(__FILE__)."/ApiHelper.php";

include_once "../php/lib/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

error_reporting(E_ALL & ~E_DEPRECATED);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
$preferred_redirect_url = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : null;
$protocol = (isset($_SERVER['HTTPS']) || FORCE_HTTPS) ? "https:" : "http:";
$redirect_url = $protocol."//".$_SERVER['HTTP_HOST'];

$success = false;

if (!isset($_REQUEST['oauth_token']) && !isset($_REQUEST['oauth_verifier']) && !isset($_REQUEST['denied'])) {
	// Need auth.
	$self_url = $protocol."//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $self_url));

	$redirect_url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
} else {
	if (!isset($_REQUEST['denied'])) {
		$oauth_token = $_REQUEST['oauth_token'];
		$oauth_verifier = $_REQUEST['oauth_verifier'];
		$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, 
			$oauth_token, $oauth_verifier);
		$access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier]);
		$twitterNickname = $access_token['screen_name'];

		// Update or set Twitter profile from profile settings.
		if ($action == "update_existing" || $action == null) {
			$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
			$request_key = isset($_REQUEST['request_key']) ? $_REQUEST['request_key'] : null;

			if ($email != null && $request_key != null) {
				$user = UserManager::getByEmail($email);
				$key = hash("sha256", $user["email"].$user["password"]);

				if ($request_key == hash("sha256", $key)) {
					UserManager::setTwitterName($user["id"], $twitterNickname);
					$success = true;
				}
			}

			$redirect_url = $protocol.TWITTER_REDIRECT_URL;
			if ($preferred_redirect_url != null) {
				$redirect_url = $preferred_redirect_url
					."?success=".var_export($success, true);
			}
		} 
		// Log in with Twitter
		else if ($action == "login") {
			$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, 
				$access_token['oauth_token'], $access_token['oauth_token_secret']);

			$twitterCredentials = $connection->get('account/verify_credentials', ['include_email' => 'true']);
			$twitterEmail = $twitterCredentials->email;
			$twitterName = $twitterCredentials->name;

			$email = $twitterEmail != null ? $twitterEmail : $twitterNickname."@twitter.com";
			
			$user = UserManager::getByEmail($email);

			if ($user == null) {
				$signupData = [
					"email" => $email,
					"password" => base64_encode(openssl_random_pseudo_bytes(32)),
					"name" => $twitterName
				];
				$user = UserManager::register($signupData);
				UserManager::setTwitterName($user["id"], $twitterNickname);
			} else {
				$user["key"] = hash("sha256", $user["email"].$user["password"]);
			}

			$key = null;
			if ($user != null) {
				$key = $user["key"];
				setcookie("credentials", '{"email":"'.$email.'","key":"'.$key.'"}', 2147483647, "/");
				$success = true;
			}

			if ($preferred_redirect_url != null) {
				$redirect_url = $preferred_redirect_url
					."?success=".var_export($success, true);
				if ($success) {
					$redirect_url = $redirect_url."&email=".$email."&key=".$key;
				}
			}
		}
	} else {
		if ($preferred_redirect_url != null) {
			$redirect_url = $preferred_redirect_url
				."?success=".var_export($success, true);
		}
	}
}

header("Location: ".$redirect_url);

?>