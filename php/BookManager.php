<?php

include_once dirname(__FILE__)."/DBManager.php";
include_once dirname(__FILE__)."/QuoteManager.php";
include_once dirname(__FILE__)."/lib/phpQuery.php";

class BookManager {
	static function getLiveLibInfo($url) {
		$regex = "/(?:https:\/\/)?(?:www\.)?(?:m\.)?livelib\.ru\/((?:book|work)\/\d+?.+)/";
		$matches = array(); 
		if (!preg_match_all($regex, $url, $matches)) {
			return null;
		}
		$bookPart = $matches[1][0];
		
		$content = file_get_contents("https://www.livelib.ru/".$bookPart);
		$html = phpQuery::newDocumentHTML($content);

		$book = array();

		$book["id"] = 
			intval($html->find(".bc__wrapper")->attr("data-dmp-book-id"))
				?: intval($html->find("#sources-edition-id")->val())
				?: 0;
				
		if ($book["id"] === 0) {
			return null;
		}

		$book["title"] = trim($html->find(".bc__book-title")[0]->text());

		$authors = $html->find(".bc-author>.bc-author__link");
		$authorsString = "";
		foreach ($authors as $author) {
			$authorsString = $authorsString.$author->nodeValue.", ";
		}
		if (strlen($authorsString) > 2) {
			$authorsString = substr($authorsString, 0, -2);
		}
		$book["author"] = $authorsString;
		
		$book["cover"] = $html->find("#main-image-book:first")->attr("src");

		return $book;
	}
	
	static function add($book) {
		$db = DBManager::connect();

		$query = $db->prepare("INSERT INTO books (id, title, author, cover) VALUES(:id, :title, :author, :cover)");
		$query->bindParam(":id", $book["id"]);
		$query->bindParam(":title", $book["title"]);
		$query->bindParam(":author", $book["author"]);
		$query->bindParam(":cover", $book["cover"]);
		$query->execute();

		$db = null;

		return BookManager::getById($book["id"]);
	}

	static function getById($id) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT * FROM books WHERE id = :id");
		$query->bindParam(":id", $id);
		$query->execute();

		$db = null;

		$imageProxyUrl = getenv("IMAGE_PROXY_URL");
		if ($imageProxyUrl === false) {
			$imageProxyUrl = "";
		}

		$result = DBManager::fetch($query);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]["id"] = intval($result[$i]["id"]);
			$result[$i]["cover"] = $imageProxyUrl.$result[$i]["cover"];
		}

		if (count($result) == 0) {
			return null;
		}
		return $result[0];
	}

	static function getByUserId($userId) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT books.id, `users-books`.id as pagingToken, books.title, books.author, books.cover, "
			."(SELECT COUNT(*) FROM quotes WHERE quotes.user_id = 'users-books'.user_id AND quotes.book_id = books.id) AS quotesCount "
			."FROM 'users-books', books WHERE 'users-books'.user_id = :user_id AND 'users-books'.book_id = books.id "
			."ORDER BY 'users-books'.id DESC");
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$db = null;

		$imageProxyUrl = getenv("IMAGE_PROXY_URL");
		if ($imageProxyUrl === false) {
			$imageProxyUrl = "";
		}

		$result = DBManager::fetch($query);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]["id"] = intval($result[$i]["id"]);
			$result[$i]["cover"] = $imageProxyUrl.$result[$i]["cover"];
		}

		return $result;
	}

	static function getPublicByUserId($userId) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT books.id, `users-books`.id as pagingToken, books.title, books.author, books.cover, "
			."(SELECT COUNT(*) FROM quotes WHERE quotes.user_id = 'users-books'.user_id AND quotes.book_id = books.id AND quotes.is_public = 1) AS quotesCount "
			."FROM 'users-books', books "
			."WHERE 'users-books'.user_id = :user_id AND 'users-books'.book_id = books.id AND quotesCount > 0 "
			."ORDER BY 'users-books'.id DESC");
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$db = null;

		$imageProxyUrl = getenv("IMAGE_PROXY_URL");
		if ($imageProxyUrl === false) {
			$imageProxyUrl = "";
		}
		
		$result = DBManager::fetch($query);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]["id"] = intval($result[$i]["id"]);
			$result[$i]["cover"] = $imageProxyUrl.$result[$i]["cover"];
		}

		return $result;
	}

	static function addForUser($bookId, $userId) {
		$usersBooks = BookManager::getByUserId($userId);
		foreach ($usersBooks as $book) {
			if ($book["id"] == $bookId) {
				return null;
			}
		}

		$db = DBManager::connect();

		$query = $db->prepare("INSERT INTO 'users-books' (book_id, user_id) VALUES(:book_id, :user_id)");
		$query->bindParam(":book_id", $bookId);
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$id = intval($db->lastInsertId());

		$db = null;

		return $id;
	}

	static function deleteForUser($bookId, $userId) {
		$db = DBManager::connect();

		$query = $db->prepare("DELETE FROM 'users-books' WHERE book_id = :book_id AND user_id = :user_id");
		$query->bindParam(":book_id", $bookId);
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$deleted = $query->rowCount();

		$db = null;

		QuoteManager::deleteForUsersBook($bookId, $userId);

		return $deleted;
	}
}

?>
