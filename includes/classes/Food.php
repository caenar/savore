<?php

require_once __DIR__ . '/../config/database.php';

class Food
{
  private $db;
  private $table = 'foods';

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getInstance();
  }

  public function create($name, $price, $description = '', $imageUrl = '')
  {
    $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, price, description, image_url) 
            VALUES (?, ?, ?, ?)
        ");
    return $stmt->execute([$name, $price, $description, $imageUrl]);
  }

  public function getAll($includeInactive = false)
  {
    $sql = "SELECT * FROM {$this->table}";
    if (!$includeInactive) {
      $sql .= " WHERE is_active = TRUE";
    }
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getById($id)
  {
    $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE id = ? AND is_active = TRUE
        ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function update($id, $name, $price, $description = null, $imageUrl = null)
  {
    $current = $this->getById($id);
    if (!$current) {
      return false;
    }

    $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET name = ?, price = ?, 
                description = COALESCE(?, description), 
                image_url = COALESCE(?, image_url) 
            WHERE id = ?
        ");
    return $stmt->execute([
      $name ?: $current['name'],
      $price ?: $current['price'],
      $description,
      $imageUrl,
      $id
    ]);
  }

  public function delete($id)
  {
    $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_active = FALSE 
            WHERE id = ?
        ");
    return $stmt->execute([$id]);
  }

  public function restore($id)
  {
    $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_active = TRUE 
            WHERE id = ?
        ");
    return $stmt->execute([$id]);
  }

  public function search($query)
  {
    $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE (name LIKE ? OR description LIKE ?) 
            AND is_active = TRUE
        ");
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
