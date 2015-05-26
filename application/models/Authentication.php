<?php

class AuthenticationModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function isAuthenticated()
    {
        return (isset($_SESSION['authenticated']) && $_SESSION['authenticated']);
    }

    public function isSuperUser()
    {
        return (isset($_SESSION['authenticated_user']) && $_SESSION['authenticated_user']->super);
    }

    public function authenticateCookie()
    {
        if (!isset($_COOKIE['session_token'])) {
            return false;
        }

        $user_id = $this->validateSessionToken($_COOKIE['session_token']);
        if (!$user_id) {
            setcookie('session_token', '', 0, '/');

            return false;
        }

        $users = new UsersModel($this->db);
        $user = $users->getByID($user_id);
        if ($user === false) {
            setcookie('session_token', '', 0, '/');

            return false;
        }

        $_SESSION['authenticated'] = true;
        $_SESSION['authenticated_user'] = $user;

        return $user;
    }

    public function authenticate($name, $password, $cookie)
    {
        $users = new UsersModel($this->db);
        $user = $users->getByName($name);
        if ($user === false) {
            return false;
        }

        $ok = password_verify($password, $user->password);
        if ($ok === false) {
            return false;
        }

        if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
            $users->update($user);
        }

        $_SESSION['authenticated'] = true;
        $_SESSION['authenticated_user'] = $user;

        if ($cookie) {
            $this->createSessionToken($user->id);
        }

        return $user;
    }

    public function logout()
    {
        if (!isset($_SESSION['authenticated_user'])) {
            return false;
        }
        
        if (isset($_COOKIE['session_token'])) {
            $this->removeSessionToken($_SESSION['authenticated_user']->id, $_COOKIE['session_token']);
        }
        setcookie('session_token', '', 0, '/');

        unset($_SESSION['authenticated']);
        unset($_SESSION['authenticated_user']);
    }

    private function validateSessionToken($token)
    {
        if (strlen($token) < 65) {
            return false;
        }

        $user_id = substr($token, 64);
        $token = substr($token, 0, 64);

        $sql = "SELECT * FROM user_session_tokens WHERE user_id = :user_id AND token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue('token', $token, SQLITE3_TEXT);

        $result = $stmt->execute();
        if ($result === false) {
            return false;
        }

        $row = $result->fetchArray(SQLITE3_ASSOC);
        if ($row === false) {
            return false;
        }

        return $row['user_id'];
    }

    private function createSessionToken($user_id)
    {
        $token = '';
        for ($i = 0; $i < 64; $i++) {
            $token .= dechex(mt_rand(0, 15));
        }

        $sql = "INSERT INTO user_session_tokens (user_id, token, created) VALUES (:user_id, :token, date('now'))";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue('token', $token, SQLITE3_TEXT);

        $result = $stmt->execute();
        
        $token .= $user_id;

        setcookie('session_token', $token, time() + 24 * 60 * 60 * 365, '/');

        return $result;
    }

    private function removeSessionToken($user_id, $token)
    {
        $sql = "DELETE FROM user_session_tokens WHERE user_id = :user_id AND token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue('token', $token, SQLITE3_TEXT);

        $result = $stmt->execute();

        return $result;
    }
}
