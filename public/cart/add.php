<?php

require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/Cart.php';
require_once __DIR__ . '/../../includes/classes/User.php';

header('Content-Type: application/json');
session_start();

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
  }

  if (!isset($_SESSION['user'])) {
    throw new Exception('Please login to add items to cart');
  }

  $data = json_decode(file_get_contents('php://input'), true);
  $foodId = $data['food_id'] ?? null;
  $quantity = $data['quantity'] ?? 1;

  if (!$foodId) {
    throw new Exception('Food item ID is required');
  }

  $userId = $_SESSION['user']['id'];
  $cart = new Cart();
  $cartId = $cart->getUserCart($userId);

  $cart->addItem($cartId, $foodId, $quantity);

  $cartCount = count($cart->getContents($cartId));

  echo json_encode([
    'success' => true,
    'cart_count' => $cartCount,
    'message' => 'Item added to cart'
  ]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
