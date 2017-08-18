<?php

include_once "../php/QuoteManager.php";
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
} else {
    error(ERROR_BAD_REQUEST);
}

?>