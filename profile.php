<?php

include_once dirname(__FILE__)."/php/UserManager.php";

setlocale(LC_ALL, "ru_RU.UTF-8");

session_start();
if (!isset($_SESSION["user"])) {
	header("Location: ./login");
	exit();
}
$user = $_SESSION["user"];

$statusText = "";
$statusClass = "";
$showStatus = false;

if (isset( $_POST["token"])) {
	$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
	$twitterUser = json_decode($s, true);
	$twitterName = explode("/", $twitterUser["identity"])[3];

	UserManager::setTwitterName($user["id"], $twitterName);
	$user["twitter_name"] = $twitterName;
	$_SESSION["user"] = $user;
}

$activeTab = 2;

include_once dirname(__FILE__)."/templates/profile.html";

?>