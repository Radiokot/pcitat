<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

$bodyJson = getJsonBody();
if ($_SERVER["REQUEST_METHOD"] !== "POST" || $bodyJson === null) {
    error(ERROR_BAD_REQUEST);
}

$email = ((isset($bodyJson["email"])) ? trim($bodyJson["email"]) : "");
$password = ((isset($bodyJson["password"])) ? trim($bodyJson["password"]) : "");
$name = ((isset($bodyJson["name"])) ? trim($bodyJson["name"]) : "");

$existingUser = UserManager::getByEmail($email);
if ($existingUser !== null) {
    error(ERROR_CONFLICT);
}

$signupData = [
	"email" => $email,
	"password" => $password,
	"name" => $name
];
$user = UserManager::register($signupData);

if ($user === null) {
    error(ERROR_NOT_FOUND);
}

response([
    "id" => $user["id"],
    "email" => $user["email"],
    "key" => $user["key"],
    "name" => $user["name"],
    "twitter" => [
        "username" => $user["twitter_name"],
        "book" => $user["twitter_book_id"]
    ]
]);

?>