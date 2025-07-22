<?php
require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/Cart.php';
require_once __DIR__ . '/../includes/classes/User.php';
require_once __DIR__ . '/../includes/classes/Order.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/checkout.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$user = $_SESSION['user'];
$cart = new Cart();
$order = new Order();

$cartId = $cart->getUserCart($userId);
$cartItems = $cart->getContents($cartId);
$subtotal = array_reduce($cartItems, fn ($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
$tax = $subtotal * 0.08; // Example 8% tax
$total = $subtotal + $tax;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $orderId = $order->createFromCart($userId, $cartId);

        $paymentMethod = $_POST['payment_method'];
        $order->processPayment($orderId, $total, $paymentMethod);

        $cart->clearCart($cartId);

        header("Location: /order-confirmation.php?id=$orderId");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto max-w-6xl">
      <h1 class="text-3xl font-bold text-savore-dark mb-8">Checkout</h1>

      <?php if (empty($cartItems)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
          <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-4"></i>
          <h2 class="text-xl font-semibold mb-2">Your cart is empty</h2>
          <p class="text-gray-600 mb-4">There's nothing to checkout</p>
          <a href="/menu.php" class="inline-block bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-full transition">
            Browse Menu
          </a>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div class="lg:col-span-2 bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-xl font-semibold">Order Summary</h2>
            </div>

            <div class="divide-y divide-gray-200">
              <?php foreach ($cartItems as $item): ?>
                <div class="p-4 flex items-center gap-4">
                  <img src="https://picsum.photos/seed/food-<?= $item['food_id'] ?>/100/100"
                    alt="<?= htmlspecialchars($item['name']) ?>"
                    class="w-16 h-16 object-cover rounded-lg">

                  <div class="flex-1">
                    <h3 class="font-medium"><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="text-gray-600 text-sm"><?= $item['quantity'] ?> × $<?= number_format($item['price'], 2) ?></p>
                  </div>

                  <div class="font-semibold">
                    $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="p-6 border-t border-gray-200">
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span>Subtotal</span>
                  <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="flex justify-between">
                  <span>Tax (8%)</span>
                  <span>$<?= number_format($tax, 2) ?></span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2">
                  <span>Total</span>
                  <span>$<?= number_format($total, 2) ?></span>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-xl font-semibold">Payment Information</h2>
            </div>

            <form method="POST" class="p-6">
              <?php if (isset($error)): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                  <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                </div>
              <?php endif; ?>

              <div class="mb-6">
                <h3 class="font-medium mb-3">Contact Information</h3>
                <div class="space-y-4">
                  <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
                  </div>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="font-medium mb-3">Payment Method</h3>
                <div class="space-y-3">
                  <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:border-savore-primary">
                    <input type="radio" name="payment_method" value="card" checked class="text-savore-primary focus:ring-savore-primary">
                    <span>Credit/Debit Card</span>
                    <div class="flex-1 flex justify-end space-x-2">
                      <i class="fab fa-cc-visa text-gray-400"></i>
                      <i class="fab fa-cc-mastercard text-gray-400"></i>
                      <i class="fab fa-cc-amex text-gray-400"></i>
                    </div>
                  </label>

                  <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:border-savore-primary">
                    <input type="radio" name="payment_method" value="paypal" class="text-savore-primary focus:ring-savore-primary">
                    <span>PayPal</span>
                    <div class="flex-1 flex justify-end">
                      <i class="fab fa-cc-paypal text-gray-400"></i>
                    </div>
                  </label>

                  <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:border-savore-primary">
                    <input type="radio" name="payment_method" value="cash" class="text-savore-primary focus:ring-savore-primary">
                    <span>Cash on Delivery</span>
                  </label>
                </div>
              </div>

              <div id="card-fields" class="mb-6 space-y-4">
                <div>
                  <label class="block text-gray-700 mb-1">Card Number</label>
                  <input type="text" name="card_number" placeholder="1234 5678 9012 3456"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary">
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-gray-700 mb-1">Expiry Date</label>
                    <input type="text" name="card_expiry" placeholder="MM/YY"
                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary">
                  </div>
                  <div>
                    <label class="block text-gray-700 mb-1">CVV</label>
                    <input type="text" name="card_cvv" placeholder="123"
                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary">
                  </div>
                </div>
              </div>

              <button type="submit" class="w-full bg-savore-primary hover:bg-savore-dark text-white font-bold py-3 px-4 rounded-lg transition">
                Complete Order
              </button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php include '../templates/footer.php'; ?>

  <script>
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
      radio.addEventListener('change', function() {
        const cardFields = document.getElementById('card-fields');
        cardFields.style.display = this.value === 'card' ? 'block' : 'none';
      });
    });

    document.getElementById('card-fields').style.display =
      document.querySelector('input[name="payment_method"]:checked').value === 'card' ? 'block' : 'none';
  </script>
</body>

</html>
