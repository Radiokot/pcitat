<?php

include_once dirname(__FILE__)."/DBManager.php";

class UserManager {
	static function getUserByEmail($email) {
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

	static function register($registerData) {
		if (UserManager::getUserByEmail($registerData["email"]) != null) {
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

		return UserManager::getUserByEmail($registerData["email"]);
	}

	static function login($loginData) {
		$user = UserManager::getUserByEmail($loginData["email"]);
		$passwordHash = hash("sha256", $user["salt"].$loginData["password"]);
		if ($user == null || $user["password"] !== $passwordHash) {
			return null;
		}
		return $user;
	}
}

?>