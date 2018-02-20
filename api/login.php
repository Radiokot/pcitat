<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

session_start();

$bodyJson = getJsonBody();
if ($_SERVER["REQUEST_METHOD"] !== "POST" || $bodyJson === null) {
    error(ERROR_BAD_REQUEST);
}

$email = ((isset($bodyJson["email"])) ? trim($bodyJson["email"]) : "");
$password = ((isset($bodyJson["password"])) ? trim($bodyJson["password"]) : "");

$user = UserManager::login(["email" => $email, "password" => $password]);

if ($user === null) {
    error(ERROR_NOT_FOUND);
}

$_SESSION[PC_USER] = $user;

response([
    "id" => $user["id"],
    "email" => $user["email"],
    "name" => $user["name"],
    "twitter" => [
        "username" => $user["twitter_name"],
        "book" => $user["twitter_book_id"]
    ],
    "session" => session_id()
]);

?>