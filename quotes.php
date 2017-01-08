<?php

include_once dirname(__FILE__)."/php/BookManager.php";
include_once dirname(__FILE__)."/php/QuoteManager.php";

setlocale(LC_ALL, "ru_RU.UTF-8");

session_start();
if (!isset($_SESSION["user"])) {
	header("Location: ./login");
	exit();
}
$user = $_SESSION["user"];

$bookId = mysql_escape_string((isset($_REQUEST["book"])) ? trim($_REQUEST["book"]) : "");
$action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : "";

$statusText = "";
$statusClass = "";
$showStatus = false;


$book = BookManager::getById($bookId);
$quotes;

if ($action === "add" && $book !== null) {
	$showStatus = true;

	$text = mysql_escape_string(htmlspecialchars((isset($_REQUEST["quoteText"])) ? trim($_REQUEST["quoteText"]) : ""));
	$text = str_replace("\\r\\n", "\n", $text);
	$text = str_replace("\\n", "\n", $text);

	$quote = QuoteManager::add($user["id"], $book["id"], $text);
	if ($quote) {
		$statusText = "Цитата добавлена";
		$statusClass = "alert-success";
		header("Location: ${_SERVER['REQUEST_URI']}");
	}
} else if ($action === "delete") {
	$id = mysql_escape_string((isset($_REQUEST["deleteQuoteId"])) ? trim($_REQUEST["deleteQuoteId"]) : "");
	QuoteManager::deleteById($id, $user["id"]);
	header("Location: ${_SERVER['REQUEST_URI']}");
}

if ($book === null) {
	$activeTab = 1;
	$quotes = QuoteManager::getByUserId($user["id"]);
} else {
	$activeTab = -1;
	$quotes = QuoteManager::getByUserId($user["id"], $book["id"]);
}

include_once dirname(__FILE__)."/templates/quotes.html";

?>