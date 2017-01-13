<?php

include_once dirname(__FILE__)."/.credentials.php";
include_once dirname(__FILE__)."/DBManager.php";
include_once dirname(__FILE__)."/UserManager.php";
include_once dirname(__FILE__)."/QuoteManager.php";

include_once dirname(__FILE__)."/lib/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

function getMentions($sinceId) {
	$twitterApi = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_OAUTH_TOKEN, TWITTER_OAUTH_SECRET);
	$mentions = $twitterApi->get("statuses/mentions_timeline", 
		["include_entities" => true,
		"since_id" => $sinceId]);
	return $mentions;
}

function getLastTweetId() {
	$db = DBManager::connect();
	$lastTweetId = DBManager::fetch($db->query("SELECT id FROM last_tweet"))[0]["id"];
	$db = null;
	return $lastTweetId;
}

function saveLastTweetId($id) {
	$db = DBManager::connect();
	$db->exec("UPDATE last_tweet SET id = ".$id);
	$db = null;
}

function getQuote($url) {
	if ($url === "") {
		return null;
	}

	$html = file_get_contents($url);
	$matches = array(); 
	if (!preg_match_all("/\"content\":\"(.+?)\"/", $html, $matches)) {
		return null;
	}
	$quote = html_entity_decode($matches[1][0]);
	return $quote;
}


$lastTweetId = getLastTweetId();
$tweets = getMentions($lastTweetId);

echo("Got ".count($tweets)." tweets from ${lastTweetId}\n");

for ($i = count($tweets) - 1; $i >= 0; $i--) { 
	$tweet = $tweets[$i];

	$lastTweetId = $tweet->id_str;

	$user = UserManager::getByTwitterName($tweet->user->screen_name);
	if ($user === null || $user["twitter_book_id"] === null) {
		continue;
	}

	$urls = $tweet->entities->urls;

	$amazonUrl = "";
	foreach ($urls as $url) {
		if (preg_match("/http:\/\/amzn.com\/k\/.+/", $url->expanded_url) == 1) {
			$amazonUrl = $url->expanded_url;
			break;
		}
	}

	$quote = getQuote($amazonUrl);
	if ($quote === null) {
		continue;
	}

	echo("Add quote for user ${user['id']} in book ${user['twitter_book_id']}\n");
	QuoteManager::add($user["id"], $user["twitter_book_id"], $quote);
}

saveLastTweetId($lastTweetId);

?>