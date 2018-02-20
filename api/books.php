<?php

include_once "../php/BookManager.php";
include_once dirname(__FILE__)."/ApiHelper.php";
error_reporting(E_ALL & ~E_DEPRECATED);

$user = getUserOrError();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bodyJson = getJsonBody();
    if ($bodyJson === null) {
        error(ERROR_BAD_REQUEST);
    }

    $url = ((isset($bodyJson["url"])) ? trim($bodyJson["url"]) : "");
    if ($url === "") {
        error(ERROR_BAD_REQUEST);
    }

    $book = BookManager::getLiveLibInfo($url);
    if ($book == null) {
        error(ERROR_NOT_FOUND);
    }

    $existBook = BookManager::getById($book["id"]);
    if ($existBook === null) {
        $existBook = BookManager::add($book);
    }

    $result = BookManager::addForUser($existBook["id"], $user["id"]);
    if (!$result) {
        error(ERROR_CONFLICT);
    } else {
        response($existBook);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $bookId = ((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");

    if ($bookId === "") {
        $books = BookManager::getByUserId($user["id"]);
        responseArray($books);
    } else {
        $book = BookManager::getById($bookId);
        if ($book == null) {
            error(ERROR_NOT_FOUND);
        }
        response($book);
    }
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $bookId = ((isset($_REQUEST["id"])) ? trim($_REQUEST["id"]) : "");

    $deleted = BookManager::deleteForUser($bookId, $user["id"]);
    response([
        "deleted" => $deleted
    ]);
} else {
    error(ERROR_BAD_REQUEST);
}

?>