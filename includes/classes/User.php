<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getInstance();
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            throw new Exception("Email and password are required");
        }

        $stmt = $this->db->prepare("
        SELECT * FROM {$this->table} 
        WHERE email = ? 
        LIMIT 1
    ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Invalid credentials");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }

        unset($user['password']);

        return $user;
    }

    public function register($username, $email, $password)
    {
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("All fields are required");
        };

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare("INSERT INTO $this->table (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }

    public function update($userId, array $data)
    {
        if (empty($data)) {
            throw new Exception("No data provided for update");
        }

        $allowedFields = ['username', 'email', 'password', 'role_id'];
        $filteredData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($filteredData)) {
            throw new Exception("No valid fields provided for update");
        }

        if (isset($filteredData['password'])) {
            $filteredData['password'] = password_hash($filteredData['password'], PASSWORD_BCRYPT);
        }

        $setClause = implode(', ', array_map(function ($field) {
            return "{$field} = :{$field}";
        }, array_keys($filteredData)));

        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($filteredData as $field => $value) {
            $stmt->bindValue(":{$field}", $value);
        }
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $stmt = $this->db->prepare("SELECT password FROM {$this->table} WHERE id = ?");
        $stmt->execute([$userId]);
        $currentHash = $stmt->fetchColumn();

        if (!password_verify($currentPassword, $currentHash)) {
            throw new Exception("Current password is incorrect");
        }

        return $this->update($userId, [
          'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }

    public function isAdmin($userId)
    {
        $stmt = $this->db->prepare("SELECT roles.name FROM users JOIN roles ON users.role_id = roles.id WHERE users.id = ?");
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();
        return ($role === "admin");
    }

    public function getUserWithAddresses($userId)
    {
        $user = $this->getById($userId);
        if (!$user) {
            return null;
        }

        $address = new Address();
        $user['addresses'] = $address->getByUser($userId);
        $user['default_address'] = $address->getDefault($userId);

        return $user;
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email, created_at 
            FROM {$this->table} 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($userId)
    {
        $stmt = $this->db->prepare("SELECT id, username, email, created_at FROM $this->table WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($userId)
    {
        $stmt = $this->db->prepare("DELETE FROM $this->table WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}
