<?php

require_once __DIR__ . '/../config/database.php';

class Order
{
  private $db;
  private $table = 'orders';

  public function __construct()
  {
    $database = new Database();
    $this->db = $database->getInstance();
  }

  public function createFromCart($userId, $cartId)
  {
    try {
      $this->db->beginTransaction();

      $cartItems = $this->getCartItems($cartId);
      $total = $this->calculateTotal($cartItems);

      $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (user_id, total, status) 
                VALUES (?, ?, 'pending')
            ");
      $stmt->execute([$userId, $total]);
      $orderId = $this->db->lastInsertId();

      $this->addOrderItems($orderId, $cartItems);

      $this->clearCart($cartId);

      $this->db->commit();
      return $orderId;
    } catch (Exception $e) {
      $this->db->rollBack();
      throw new Exception("Order failed: " . $e->getMessage());
    }
  }

  public function processPayment($orderId, $amount, $method = 'card')
  {
    $stmt = $this->db->prepare("
            INSERT INTO payments (order_id, amount, method, status) 
            VALUES (?, ?, ?, 'completed')
        ");

    if ($stmt->execute([$orderId, $amount, $method])) {
      $this->updateOrderStatus($orderId, 'paid');
      return true;
    }
    return false;
  }

  public function getHistory($userId)
  {
    $stmt = $this->db->prepare("
            SELECT o.*, p.method as payment_method 
            FROM {$this->table} o
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getDetails($orderId)
  {
    $stmt = $this->db->prepare("
            SELECT o.*, p.method as payment_method, p.status as payment_status
            FROM {$this->table} o
            LEFT JOIN payments p ON o.id = p.order_id
            WHERE o.id = ?
        ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
      $stmt = $this->db->prepare("
                SELECT oi.*, f.name as food_name 
                FROM order_items oi
                JOIN foods f ON oi.food_id = f.id
                WHERE oi.order_id = ?
            ");
      $stmt->execute([$orderId]);
      $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $order;
  }

  private function getCartItems($cartId)
  {
    $stmt = $this->db->prepare("
            SELECT ci.*, f.price, f.name 
            FROM cart_items ci
            JOIN foods f ON ci.food_id = f.id
            WHERE ci.cart_id = ?
        ");
    $stmt->execute([$cartId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  private function calculateTotal($cartItems)
  {
    return array_reduce($cartItems, function ($total, $item) {
      return $total + ($item['price'] * $item['quantity']);
    }, 0);
  }

  private function addOrderItems($orderId, $cartItems)
  {
    $stmt = $this->db->prepare("
            INSERT INTO order_items (order_id, food_id, quantity, price_at_order)
            VALUES (?, ?, ?, ?)
        ");

    foreach ($cartItems as $item) {
      $stmt->execute([
        $orderId,
        $item['food_id'],
        $item['quantity'],
        $item['price']
      ]);
    }
  }

  private function clearCart($cartId)
  {
    $stmt = $this->db->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cartId]);
  }

  private function updateOrderStatus($orderId, $status)
  {
    $stmt = $this->db->prepare("
            UPDATE {$this->table} SET status = ? WHERE id = ?
        ");
    $stmt->execute([$status, $orderId]);
  }
}
