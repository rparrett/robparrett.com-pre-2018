<?php

class UsersModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getByID($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";

        $stmt = $this->db->prepare($sql);
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

    public function getByName($name)
    {
        $sql = "SELECT * FROM users WHERE name = :name";

        $stmt = $this->db->prepare($sql);
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

    public function update($user)
    {
        $sql = "UPDATE users SET name = :name, password = :password, super = :super WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('id', $user->id, SQLITE3_INTEGER);
        $stmt->bindValue('password', $user->password, SQLITE3_TEXT);
        $stmt->bindValue('super', $user->super ? 1 : 0, SQLITE3_INTEGER);
        $stmt->bindValue('name', $user->name, SQLITE3_TEXT);

        $result = $stmt->execute();

        return $result;
    }

    public function add($name, $password, $super)
    {
        $sql = "INSERT INTO users (name, password, super) VALUES (:name, :password, :super)";

        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('name', $name, SQLITE3_TEXT);
        $stmt->bindValue('password', $password, SQLITE3_TEXT);
        $stmt->bindValue('super', $super ? 1 : 0, SQLITE3_INTEGER);

        $result = $stmt->execute();

        return $result;
    }
}
