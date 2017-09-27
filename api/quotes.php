<?php

include_once "../php/QuoteManager.php";
include_once "../php/BookManager.php";
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

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $bookId = mysql_escape_string((isset($_REQUEST["book"])) ? trim($_REQUEST["book"]) : "");
    
    $quotes;
    if ($bookId === "") {
        $quotes = QuoteManager::getByUserId($user["id"]);
    } else {
        $quotes = QuoteManager::getByUserId($user["id"], $bookId);
    }
    
    responseArray($quotes);
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookId = mysql_escape_string((isset($_REQUEST["book"])) ? trim($_REQUEST["book"]) : "");
    $book = BookManager::getById($bookId);

    if ($book === null) {
        error(ERROR_BAD_REQUEST);
    }

    $bodyJson = getJsonBody();
    if ($bodyJson === null) {
        error(ERROR_BAD_REQUEST);
    }

    $text = isset($bodyJson["text"]) ? trim($bodyJson["text"]) : "";

    if ($text === "") {
        error(ERROR_BAD_REQUEST);
    }

    $quote = QuoteManager::add($user["id"], $book["id"], $text);
    if (!$quote) {
        error(ERROR_SERVER);
    } else {
        response($quote);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
    $quoteId = mysql_escape_string((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");

    if ($quoteId === "") {
        error(ERROR_BAD_REQUEST);
    }

    $bodyJson = getJsonBody();
    if ($bodyJson === null) {
        error(ERROR_BAD_REQUEST);
    }

    $text = isset($bodyJson["text"]) ? trim($bodyJson["text"]) : "";

    if ($text === "") {
        error(ERROR_BAD_REQUEST);
    }
    
    $updatedQuote = QuoteManager::update($quoteId, $user["id"], $text);
    if (!$updatedQuote) {
        error(ERROR_NOT_FOUND);
    } else {
        response($updatedQuote);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
     $quoteId = mysql_escape_string((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");

     $deleted = QuoteManager::deleteById($quoteId, $user["id"]);

     response([
        "deleted" => $deleted
    ]);
} else {
    error(ERROR_BAD_REQUEST);
}

?>