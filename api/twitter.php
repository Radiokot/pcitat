<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

session_id(array_keys($_GET)[0]);
session_start();

if (!isset($_SESSION[PC_USER])) {
    error(ERROR_NOT_AUTHORIZED);
}
$user = $_SESSION[PC_USER];

if (isset( $_POST["token"])) {
	$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
	$twitterUser = json_decode($s, true);
	$twitterName = explode("/", $twitterUser["identity"])[3];

	UserManager::setTwitterName($user["id"], $twitterName);
	$user["twitter_name"] = $twitterName;
	$_SESSION[PC_USER] = $user;
}


header("Location: /");

?>