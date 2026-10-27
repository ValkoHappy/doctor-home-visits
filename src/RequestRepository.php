<?php
final class RequestRepository
{
    public function __construct(private PDO $db) {}

    public function create(int $userId, string $name, string $address, string $symptoms): void
    {
        $statement = $this->db->prepare('INSERT INTO requests (user_id, patient_name, address, symptoms) VALUES (?, ?, ?, ?)');
        $statement->execute([$userId, $name, $address, $symptoms]);
    }

    public function changeStatus(int $id, string $status): void
    {
        $statement = $this->db->prepare('UPDATE requests SET status = ? WHERE id = ?');
        $statement->execute([$status, $id]);
    }

    public function forUser(int $id): array
    {
        $statement = $this->db->prepare('SELECT * FROM requests WHERE user_id = ? ORDER BY created_at DESC, id DESC');
        $statement->execute([$id]);
        return $statement->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT requests.*, users.email, users.name AS user_name, users.phone FROM requests JOIN users ON users.id = requests.user_id ORDER BY requests.created_at DESC, requests.id DESC')->fetchAll();
    }
}
