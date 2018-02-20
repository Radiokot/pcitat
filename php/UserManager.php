<?php

include_once dirname(__FILE__)."/DBManager.php";

class UserManager {
	static function getByEmail($email) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT * FROM users WHERE email LIKE :email");
		$query->bindParam(":email", $email);
		$query->execute();

		$db = null;

		$result = DBManager::fetch($query);
		if (count($result) == 0) {
			return null;
		}

		return $result[0];

	}

	static function getByTwitterName($twitterName) {
		$db = DBManager::connect();

		$query = $db->prepare("SELECT * FROM users WHERE twitter_name LIKE :twitter_name");
		$query->bindParam(":twitter_name", $twitterName);
		$query->execute();

		$db = null;

		$result = DBManager::fetch($query);
		if (count($result) == 0) {
			return null;
		}

		return $result[0];

	}

	static function setTwitterName($userId, $twitterName) {
		$db = DBManager::connect();

		$query = $db->prepare("UPDATE users SET twitter_name = :twitter_name WHERE id = :user_id");
		$query->bindParam(":twitter_name", $twitterName);
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$db = null;
	}

	static function setTwitterBook($userId, $bookId) {
		$db = DBManager::connect();

		$query = $db->prepare("UPDATE users SET twitter_book_id = :twitter_book_id WHERE id = :user_id");
		if ($bookId !== null) {
			$query->bindParam(":twitter_book_id", $bookId);
		} else {
			$query->bindParam(":twitter_book_id", $bookId, PDO::PARAM_NULL);
		}
		$query->bindParam(":user_id", $userId);
		$query->execute();

		$db = null;
	}

	static function register($registerData) {
		if (UserManager::getByEmail($registerData["email"]) != null) {
			return null;
		}

		$db = DBManager::connect();

		$salt = base64_encode(openssl_random_pseudo_bytes(16));
		$passwordHash = hash("sha256", $salt.$registerData["password"]);

		$query = $db->prepare("INSERT INTO users (name, email, password, salt) VALUES(:name, :email, :password, :salt)");
		$query->bindParam(":name", $registerData["name"]);
		$query->bindParam(":email", $registerData["email"]);
		$query->bindParam(":password", $passwordHash);
		$query->bindParam(":salt", $salt);
		$query->execute();

		$db = null;

		$user = UserManager::getByEmail($registerData["email"]);
		$user["key"] = hash("sha256", $user["email"].$user["password"]);
		return $user;
	}

	static function login($loginData) {
		$user = UserManager::getByEmail($loginData["email"]);
		$passwordHash = hash("sha256", $user["salt"].$loginData["password"]);
		if ($user == null || $user["password"] !== $passwordHash) {
			return null;
		}
		$user["key"] = hash("sha256", $user["email"].$user["password"]);
		return $user;
	}
}

?>