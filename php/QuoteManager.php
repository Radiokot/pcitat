<?php

include_once dirname(__FILE__)."/DBManager.php";

class QuoteManager {
	static function add($userId, $bookId, $isPublic, $text) {
		$db = DBManager::connect();

		$query = $db->prepare("INSERT INTO quotes (user_id, book_id, is_public, text) VALUES(:user_id, :book_id, :is_public, :text)");
		$query->bindParam(":user_id", $userId);
		$query->bindParam(":book_id", $bookId);
		$query->bindValue(":is_public", $isPublic ? 1 : 0);
		$query->bindParam(":text", $text);
		$query->execute();

		$id = intval($db->lastInsertId());

		$db = null;

		return QuoteManager::getById($id);
	}

	static function update($id, $userId, $isPublic, $text) {
		$db = DBManager::connect();

		$query = $db->prepare("UPDATE quotes SET text = :text, is_public = :is_public WHERE id = :id AND user_id = :user_id");
		$query->bindParam(":id", $id);
		$query->bindParam(":user_id", $userId);
		$query->bindValue(":is_public", ($isPublic) ? 1 : 0);
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

		$deleted = $query->rowCount();

		$db = null;

		return $deleted;
	} 

	static function deleteForUsersBook($bookId, $userId) {
		$db = DBManager::connect();

		$query = $db->prepare("DELETE FROM quotes WHERE book_id = :book_id AND user_id = :user_id");
		$query->bindParam(":book_id", $bookId);
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$deleted = $query->rowCount();

		$db = null;

		return $deleted;
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
			$result[$i]["is_public"] = boolval($result[$i]["is_public"]);
		}

		if (count($result) == 0) {
			return null;
		}
		return $result[0];
	} 

	static function getByUserId($userId, $publicOnly, $bookId=null) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT quotes.id, quotes.user_id, quotes.is_public, quotes.text, quotes.book_id as bookId, books.title AS bookTitle "
			."FROM quotes, books WHERE user_id = :user_id "
			."AND quotes.book_id = books.id "
			.(($publicOnly === true ? "AND quotes.is_public = 1 " : " "))
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
			$result[$i]["is_public"] = boolval($result[$i]["is_public"]);
		}

		return $result;
	}
}

?>