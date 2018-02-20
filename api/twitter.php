<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

$params = explode("|", array_keys($_GET)[0]);
$_SERVER[EMAIL_HEADER] = $params[0];
$_SERVER[KEY_HEADER] = $params[1];

$user = getUserOrError();

if (isset( $_POST["token"])) {
	$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
	$twitterUser = json_decode($s, true);
	$twitterName = explode("/", $twitterUser["identity"])[3];

	UserManager::setTwitterName($user["id"], $twitterName);
}

header("Location: /");

?>