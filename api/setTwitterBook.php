<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if (isset($_SERVER["HTTP_X_SESSION"])) {
    session_id($_SERVER["HTTP_X_SESSION"]);
}
session_start();

if (!isset($_SESSION[PC_USER])) {
    error(ERROR_NOT_AUTHORIZED);
}
$user = $_SESSION[PC_USER];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error(ERROR_BAD_REQUEST);
}

$bodyJson = getJsonBody();
if ($bodyJson === null) {
    error(ERROR_BAD_REQUEST);
}

$bookId = (htmlspecialchars((isset($bodyJson["book"])) ? trim($bodyJson["book"]) : ""));
if ($bookId === "") {
    error(ERROR_BAD_REQUEST);
}

UserManager::setTwitterBook($user["id"], $bookId);
$user["twitter_book_id"] = $bookId;

$_SESSION[PC_USER] = $user;

response([
    "success" => true
]);

?>