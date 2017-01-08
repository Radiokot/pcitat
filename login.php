<?php

include_once dirname(__FILE__)."/php/UserManager.php";
error_reporting(E_ALL & ~E_DEPRECATED);

session_start();

$action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : "";

$statusText = "";
$statusClass = "";
$showStatus = true;

$prevEmail = "";
$prevPassword = "";

if ($action === "") {
	$showStatus = false;
} else if ($action === "login") {
	$loginEmail = mysql_escape_string((isset($_REQUEST["email"])) ? trim($_REQUEST["email"]) : "");
	$loginPassword = mysql_escape_string((isset($_REQUEST["password"])) ? $_REQUEST["password"] : "");

	$user = UserManager::login(["email" => $loginEmail, "password" => $loginPassword]);
	if ($user === null) {
		$statusText = "Пользователь с такими данными не существует";
		$statusClass = "alert-danger";

		$prevEmail = $loginEmail;
		$prevPassword = $loginPassword;
	} else {
		$_SESSION["user"] = $user;

		header("Location: ./");
		exit();
	}
} else if ($action === "signup") {
	$signupEmail = mysql_escape_string((isset($_REQUEST["signupEmail"])) ? trim($_REQUEST["signupEmail"]) : "");
	$signupPassword = mysql_escape_string((isset($_REQUEST["signupPassword"])) ? $_REQUEST["signupPassword"] : "");
	$signupName = mysql_escape_string((isset($_REQUEST["signupName"])) ? $_REQUEST["signupName"] : "");

	$registerData = [
	"email" => $signupEmail,
	"password" => $signupPassword,
	"name" => $signupName
	];
	$newUser = UserManager::register($registerData);

	if ($newUser !== null) {
		$statusText = "Аккаунт создан, теперь вы можете войти";
		$statusClass = "alert-success";
	} else {
		$statusText = "Невозможно создать аккаунт с такими данными";
		$statusClass = "alert-danger";
	}
}

include_once dirname(__FILE__)."/templates/login.html";

?>