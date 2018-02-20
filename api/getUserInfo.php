<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if (isset($_SERVER["HTTP_X_SESSION"])) {
    session_id($_SERVER["HTTP_X_SESSION"]);
}
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    error(ERROR_BAD_REQUEST);
}

$email = ((isset($bodyJson["email"])) ? trim($bodyJson["email"]) : "");
$password = ((isset($bodyJson["password"])) ? trim($bodyJson["password"]) : "");

if (!isset($_SESSION[PC_USER])) {
    error(ERROR_NOT_AUTHORIZED);
}
$user = $_SESSION[PC_USER];

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