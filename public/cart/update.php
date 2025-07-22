<?php

require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/Cart.php';

header('Content-Type: application/json');
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_SESSION['user'])) {
        throw new Exception('Please login to update cart');
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

    if ($quantity <= 0) {
        $cart->removeItem($cartId, $foodId);
    } else {
        $cart->updateQuantity($cartId, $foodId, $quantity);
    }

    $cartContents = $cart->getContents($cartId);
    $total = array_reduce($cartContents, function ($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    echo json_encode([
      'success' => true,
      'cart_items' => $cartContents,
      'cart_total' => $total,
      'cart_count' => count($cartContents)
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
      'success' => false,
      'message' => $e->getMessage()
    ]);
}
