<?php

class UsersModel {
	private $_db;

	function __construct($db) {
		$this->_db = $db;
	}

	function getByID($id) {
		$sql = "SELECT * FROM users WHERE id = :id";

		$stmt = $this->_db->prepare($sql);
		$stmt->bindValue('id', $id, SQLITE3_INTEGER);
		
		$result = $stmt->execute();
		if ($result === false) {
			return false;
		}

		$row = $result->fetchArray(SQLITE3_ASSOC);
		if ($row === false) {
			return false;
		}

		$row = (object) $row;
		$row->super = $row->super ? true : false;

		return $row;
	}

	function getByName($name) {
		$sql = "SELECT * FROM users WHERE name = :name";

		$stmt = $this->_db->prepare($sql);
		$stmt->bindValue('name', $name, SQLITE3_TEXT);
		
		$result = $stmt->execute();
		if ($result === false) {
			return false;
		}

		$row = $result->fetchArray(SQLITE3_ASSOC);
		if ($row === false) {
			return false;
		}

		$row = (object) $row;
		$row->super = $row->super ? true : false;

		return $row;
	}

	function update($user) {
		$sql = "UPDATE users SET name = :name, password = :password, super = :super WHERE id = :id";

		$stmt = $this->_db->prepare($sql);
		$stmt->bindValue('id', $user->id, SQLITE3_INTEGER);
		$stmt->bindValue('password', $user->password, SQLITE3_TEXT);
		$stmt->bindValue('super', $user->super ? 1 : 0, SQLITE3_INTEGER);
		$stmt->bindValue('name', $user->name, SQLITE3_TEXT);

		$result = $stmt->execute();

		return $result;
	}

	function add($name, $password, $super) {
		$sql = "INSERT INTO users (name, password, super) VALUES (:name, :password, :super)";

		$password = password_hash($password, PASSWORD_DEFAULT);

		$stmt = $this->_db->prepare($sql);
		$stmt->bindValue('name', $name, SQLITE3_TEXT);
		$stmt->bindValue('password', $password, SQLITE3_TEXT);
		$stmt->bindValue('super', $super ? 1 : 0, SQLITE3_INTEGER);

		$result = $stmt->execute();

		return $result;
	}
}
