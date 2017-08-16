<?php

include_once dirname(__FILE__)."/DBManager.php";

class QuoteManager {
	static function add($userId, $bookId, $text) {
		$db = DBManager::connect();

		$query = $db->prepare("INSERT INTO quotes (user_id, book_id, text) VALUES(:user_id, :book_id, :text)");
		$query->bindParam(":user_id", $userId);
		$query->bindParam(":book_id", $bookId);
		$query->bindParam(":text", $text);
		$query->execute();

		$id = intval($db->lastInsertId());

		$db = null;

		return QuoteManager::getById($id);
	}

	static function update($id, $userId, $text) {
		$db = DBManager::connect();

		$query = $db->prepare("UPDATE quotes SET text = :text WHERE id = :id AND user_id = :user_id");
		$query->bindParam(":id", $id);
		$query->bindParam(":user_id", $userId);
		$query->bindParam(":text", $text);
		$query->execute();

		$db = null;

		return QuoteManager::getById($id);
	}

	static function deleteById($id, $userId) {
		$db = DBManager::connect();

		$query = $db->prepare("DELETE FROM quotes WHERE id = :id AND user_id = :user_id");
		$query->bindParam(":id", $id);
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$db = null;
	} 

	static function deleteForUsersBook($bookId, $userId) {
		$db = DBManager::connect();

		$query = $db->prepare("DELETE FROM quotes WHERE book_id = :book_id AND user_id = :user_id");
		$query->bindParam(":book_id", $bookId);
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$db = null;
	} 


	static function getById($id) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT * FROM quotes WHERE id = :id");
		$query->bindParam(":id", $id);
		$query->execute();

		$db = null;

		$result = DBManager::fetch($query);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]["id"] = intval($result[$i]["id"]);
		}

		if (count($result) == 0) {
			return null;
		}
		return $result[0];
	} 

	static function getByUserId($userId, $bookId=null) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT quotes.id, quotes.user_id, quotes.text, quotes.book_id as bookId, books.title AS bookTitle "
			."FROM quotes, books WHERE user_id = :user_id "
			."AND quotes.book_id = books.id "
			.(($bookId !== null) ? "AND book_id = :book_id " : " ")
			."ORDER BY quotes.id DESC");
		$query->bindParam(":user_id", $userId);
		if ($bookId !== null) {
			$query->bindParam(":book_id", $bookId);
		}
		$query->execute();

		$db = null;

		$result = DBManager::fetch($query);
		for ($i = 0; $i < count($result); $i++) {
			$result[$i]["id"] = intval($result[$i]["id"]);
		}

		return $result;
	}
}

?>