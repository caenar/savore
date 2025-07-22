<?php

require_once __DIR__ . '/../config/database.php';

class Cart
{
  private $db;
  private $cartTable = 'carts';
  private $itemsTable = 'cart_items';

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getInstance();
  }

  public function createCart($userId)
  {
    $stmt = $this->db->prepare("
            INSERT INTO {$this->cartTable} (user_id) 
            VALUES (?)
        ");
    $stmt->execute([$userId]);
    return $this->db->lastInsertId();
  }

  public function addItem($cartId, $foodId, $quantity = 1)
  {
    $existing = $this->getItem($cartId, $foodId);

    if ($existing) {
      return $this->updateQuantity($cartId, $foodId, $existing['quantity'] + $quantity);
    } else {
      $stmt = $this->db->prepare("
                INSERT INTO {$this->itemsTable} (cart_id, food_id, quantity)
                VALUES (?, ?, ?)
            ");
      return $stmt->execute([$cartId, $foodId, $quantity]);
    }
  }

  public function updateQuantity($cartId, $foodId, $newQuantity)
  {
    if ($newQuantity <= 0) {
      return $this->removeItem($cartId, $foodId);
    }

    $stmt = $this->db->prepare("
            UPDATE {$this->itemsTable} 
            SET quantity = ? 
            WHERE cart_id = ? AND food_id = ?
        ");
    return $stmt->execute([$newQuantity, $cartId, $foodId]);
  }

  public function removeItem($cartId, $foodId)
  {
    $stmt = $this->db->prepare("
            DELETE FROM {$this->itemsTable} 
            WHERE cart_id = ? AND food_id = ?
        ");
    return $stmt->execute([$cartId, $foodId]);
  }

  public function clearCart($cartId)
  {
    $stmt = $this->db->prepare("
            DELETE FROM {$this->itemsTable} 
            WHERE cart_id = ?
        ");
    return $stmt->execute([$cartId]);
  }

  public function getContents($cartId)
  {
    $stmt = $this->db->prepare("
            SELECT ci.*, f.name, f.price, f.image_url 
            FROM {$this->itemsTable} ci
            JOIN foods f ON ci.food_id = f.id
            WHERE ci.cart_id = ? AND f.is_active = TRUE
        ");
    $stmt->execute([$cartId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTotal($cartId)
  {
    $items = $this->getContents($cartId);
    return array_reduce($items, function ($total, $item) {
      return $total + ($item['price'] * $item['quantity']);
    }, 0);
  }

  public function getItem($cartId, $foodId)
  {
    $stmt = $this->db->prepare("
            SELECT * FROM {$this->itemsTable} 
            WHERE cart_id = ? AND food_id = ?
        ");
    $stmt->execute([$cartId, $foodId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getUserCart($userId)
  {
    $stmt = $this->db->prepare("
            SELECT id FROM {$this->cartTable} 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
    $stmt->execute([$userId]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    return $cart ? $cart['id'] : $this->createCart($userId);
  }
}
