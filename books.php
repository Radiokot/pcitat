<?php

include_once dirname(__FILE__)."/php/BookManager.php";

setlocale(LC_ALL, "ru_RU.UTF-8");

session_start();
if (!isset($_SESSION["user"])) {
	header("Location: ./login");
	exit();
}
$user = $_SESSION["user"];

$action = (isset($_REQUEST["action"])) ? $_REQUEST["action"] : "";

$statusText = "";
$statusClass = "";
$showStatus = false;

$activeTab = 0;

if ($action === "add") {
	$showStatus = true;

	$url = (isset($_REQUEST["bookUrl"])) ? trim($_REQUEST["bookUrl"]) : "";
	$book = BookManager::getLiveLibInfo($url);

	if ($book === null) {
		$statusText = "Не удалось получить информацию о книге";
		$statusClass = "alert-danger";
	} else {
		if (BookManager::getById($book["id"]) === null) {
			BookManager::add($book);
		}

		$result = BookManager::addForUser($book["id"], $user["id"]);
		if (!$result) {
			$statusText = "Книга «${book["title"]}» уже в вашей коллекции";
			$statusClass = "alert-warning";
		} else {
			$statusText = "Книга «${book["title"]}» добавлена в вашу коллекцию";
			$statusClass = "alert-success";
		}
	}
} else if ($action === "delete") {
	$showStatus = true;

	$id = (isset($_REQUEST["deleteBookId"])) ? trim($_REQUEST["deleteBookId"]) : "";
	BookManager::deleteForUser($id, $user["id"]);
	header("Location: ${_SERVER['REQUEST_URI']}");
}

$books = BookManager::getByUserId($user["id"]);

include_once dirname(__FILE__)."/templates/books.html";

?>