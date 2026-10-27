<?php
final class AuthService
{
    public function __construct(private PDO $db) {}

    public function register(string $email, string $name, string $phone, string $password): bool
    {
        $statement = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $statement->execute([$email]);
        if ($statement->fetch()) return false;
        $statement = $this->db->prepare('INSERT INTO users (email, name, phone, password) VALUES (?, ?, ?, ?)');
        $statement->execute([$email, $name, $phone, password_hash($password, PASSWORD_DEFAULT)]);
        return true;
    }

    public function login(string $email, string $password): bool
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute([$email]);
        $user = $statement->fetch();
        if (!$user || !password_verify($password, $user['password'])) return false;
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        return true;
    }
}
