<?php

include_once "../php/UserManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

$user = getUserOrError();

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

response([
    "success" => true
]);

?>