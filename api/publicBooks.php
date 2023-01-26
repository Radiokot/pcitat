<?php

include_once "../php/BookManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $bookId = ((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");
    $userId = ((isset($_REQUEST["user"])) ? trim($_REQUEST["user"]) : "");

    if ($userId === "" && $bookId === "") {
        error(ERROR_BAD_REQUEST);
    }

    if ($bookId === "") {
        $books = BookManager::getPublicByUserId($userId);
        responseArray($books);
    } else {
        $book = BookManager::getById($bookId);
        if ($book == null) {
            error(ERROR_NOT_FOUND);
        }        
        response($book);
    }
} else {
    error(ERROR_BAD_REQUEST);
}
