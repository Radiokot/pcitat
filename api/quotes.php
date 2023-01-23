<?php

include_once "../php/QuoteManager.php";
include_once "../php/BookManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

$user = getUserOrError();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $bookId = ((isset($_REQUEST["book"])) ? trim($_REQUEST["book"]) : "");
    
    $quotes;
    if ($bookId === "") {
        $quotes = QuoteManager::getByUserId($user["id"]);
    } else {
        $quotes = QuoteManager::getByUserId($user["id"], $bookId);
    }
    
    responseArray($quotes);
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookId = ((isset($_REQUEST["book"])) ? trim($_REQUEST["book"]) : "");
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

    $isPublic = (isset($bodyJson["is_public"]) && is_bool($bodyJson["is_public"])) ? $bodyJson["is_public"] : false;
    
    $quote = QuoteManager::add($user["id"], $book["id"], $isPublic, $text);

    if (!$quote) {
        error(ERROR_SERVER);
    } else {
        unset($quote["book_id"]);
        $quote["bookId"] = $book["id"];
        $quote["bookTitle"] = $book["title"];

        response($quote);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "PATCH") {
    $quoteId = ((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");

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

    $isPublic = (isset($bodyJson["is_public"]) && is_bool($bodyJson["is_public"])) ? $bodyJson["is_public"] : false;
    
    $updatedQuote = QuoteManager::update($quoteId, $user["id"], $isPublic, $text);
    if (!$updatedQuote) {
        error(ERROR_NOT_FOUND);
    } else {
        response($updatedQuote);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
     $quoteId = ((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");

     $deleted = QuoteManager::deleteById($quoteId, $user["id"]);

     response([
        "deleted" => $deleted
    ]);
} else {
    error(ERROR_BAD_REQUEST);
}

?>