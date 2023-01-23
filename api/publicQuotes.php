<?php

include_once "../php/QuoteManager.php";
include_once "../php/BookManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $bookId = ((isset($_REQUEST["book"])) ? trim($_REQUEST["book"]) : "");
    $userId = ((isset($_REQUEST["user"])) ? trim($_REQUEST["user"]) : "");
    
    if ($userId === "") {
        error(ERROR_BAD_REQUEST);
    }

    $quotes;
    if ($bookId === "") {
        $quotes = QuoteManager::getByUserId($userId, true);
    } else {
        $quotes = QuoteManager::getByUserId($userId, true, $bookId);
    }
    
    responseArray($quotes);
} else {
    error(ERROR_BAD_REQUEST);
}

?>