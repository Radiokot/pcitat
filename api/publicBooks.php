<?php

include_once "../php/BookManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $userId = ((isset($_REQUEST["user"])) ? trim($_REQUEST["user"]) : "");

    if ($userId === "") {
        error(ERROR_BAD_REQUEST);
    }

    $books = BookManager::getPublicByUserId($userId);
    responseArray($books);
} else {
    error(ERROR_BAD_REQUEST);
}
