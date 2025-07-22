<?php

require_once __DIR__ . '/../config/database.php';

class Address
{
    private $db;
    private $table = 'addresses';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getInstance();
    }

    public function create($userId, $data)
    {
        $required = ['full_name', 'phone', 'address_line1', 'city', 'state', 'postal_code'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }

        if ($data['is_default']) {
            $this->db->prepare("
                UPDATE {$this->table} 
                SET is_default = FALSE 
                WHERE user_id = ?
            ")->execute([$userId]);
        }

        $data = array_merge([
            'address_line2' => '',
            'country' => 'United States',
            'is_default' => false
        ], $data);

        $sql = "INSERT INTO {$this->table} 
                (user_id, full_name, phone, address_line1, address_line2, city, state, postal_code, country, is_default) 
                VALUES (:user_id, :full_name, :phone, :address_line1, :address_line2, :city, :state, :postal_code, :country, :is_default)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address_line1', $data['address_line1']);
        $stmt->bindParam(':address_line2', $data['address_line2']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':postal_code', $data['postal_code']);
        $stmt->bindParam(':country', $data['country']);
        $stmt->bindParam(':is_default', $data['is_default'], PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? 
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDefault($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ? AND is_default = TRUE
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($addressId, $userId, $data)
    {
        $allowedFields = ['full_name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country', 'is_default'];
        $updates = [];
        $params = ['id' => $addressId, 'user_id' => $userId];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updates[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($updates)) {
            throw new Exception("No valid fields to update");
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " 
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($addressId, $userId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$addressId, $userId]);
    }

    public function setDefault($addressId, $userId)
    {
        $this->db->beginTransaction();

        try {
            // Reset current default
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_default = FALSE 
                WHERE user_id = ? AND is_default = TRUE
            ");
            $stmt->execute([$userId]);

            // Set new default
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET is_default = TRUE 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$addressId, $userId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
