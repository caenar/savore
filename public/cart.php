<?php
require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/Cart.php';
require_once __DIR__ . '/../includes/classes/User.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/cart.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$cart = new Cart();
$cartId = $cart->getUserCart($userId);
$cartItems = $cart->getContents($cartId);
$cartTotal = array_reduce($cartItems, function ($total, $item) {
    return $total + ($item['price'] * $item['quantity']);
}, 0);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto max-w-4xl">
      <h1 class="text-3xl font-bold text-savore-dark mb-8">Your Cart</h1>

      <?php if (empty($cartItems)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
          <i class="fas fa-shopping-cart text-5xl text-gray-300 mb-4"></i>
          <h2 class="text-xl font-semibold mb-2">Your cart is empty</h2>
          <p class="text-gray-600 mb-4">Start adding some delicious items from our menu</p>
          <a href="/menu.php" class="inline-block bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-full transition">
            Browse Menu
          </a>
        </div>
      <?php else: ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
          <div class="divide-y divide-gray-200">
            <?php foreach ($cartItems as $item): ?>
              <div class="p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4 cart-item" data-food-id="<?= $item['food_id'] ?>">
                <img src="https://picsum.photos/seed/food-<?= $item['food_id'] ?>/200/200"
                  alt="<?= htmlspecialchars($item['name']) ?>"
                  class="w-20 h-20 object-cover rounded-lg">

                <div class="flex-1">
                  <h3 class="font-semibold"><?= htmlspecialchars($item['name']) ?></h3>
                  <p class="text-gray-600">$<?= number_format($item['price'], 2) ?></p>
                </div>

                <div class="flex items-center gap-2">
                  <button class="quantity-btn minus bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full transition"
                    data-action="decrease">
                    <i class="fas fa-minus"></i>
                  </button>
                  <span class="quantity-display w-10 text-center"><?= $item['quantity'] ?></span>
                  <button class="quantity-btn plus bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full transition"
                    data-action="increase">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>

                <div class="font-semibold">
                  $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                </div>

                <button class="remove-btn text-red-500 hover:text-red-700 transition">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="p-6 border-t border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <span class="font-semibold">Subtotal</span>
              <span class="text-xl font-bold" id="cart-total">$<?= number_format($cartTotal, 2) ?></span>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
              <a href="/menu.php" class="flex-1 text-center border border-savore-primary text-savore-primary hover:bg-savore-primary hover:text-white font-bold py-3 px-4 rounded-lg transition">
                Continue Shopping
              </a>
              <a href="/checkout.php" class="flex-1 text-center bg-savore-primary hover:bg-savore-dark text-white font-bold py-3 px-4 rounded-lg transition">
                Proceed to Checkout
              </a>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php include '../templates/footer.php'; ?>

  <script>
    document.querySelectorAll('.quantity-btn').forEach(btn => {
      btn.addEventListener('click', async function() {
        const item = this.closest('.cart-item');
        const foodId = item.dataset.foodId;
        const quantityDisplay = item.querySelector('.quantity-display');
        let quantity = parseInt(quantityDisplay.textContent);

        if (this.dataset.action === 'increase') {
          quantity++;
        } else if (this.dataset.action === 'decrease') {
          quantity = Math.max(1, quantity - 1);
        }

        try {
          const response = await fetch('/cart/update.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              food_id: foodId,
              quantity: quantity
            })
          });

          const result = await response.json();

          if (result.success) {
            quantityDisplay.textContent = quantity;
            document.getElementById('cart-total').textContent = `$${result.cart_total.toFixed(2)}`;

            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
              cartCount.textContent = result.cart_count;
            }
          }
        } catch (error) {
          console.error('Error:', error);
        }
      });
    });

    document.querySelectorAll('.remove-btn').forEach(btn => {
      btn.addEventListener('click', async function() {
        const item = this.closest('.cart-item');
        const foodId = item.dataset.foodId;

        if (confirm('Remove this item from your cart?')) {
          try {
            const response = await fetch('/cart/remove.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify({
                food_id: foodId
              })
            });

            const result = await response.json();

            if (result.success) {
              item.remove();
              document.getElementById('cart-total').textContent = `$${result.cart_total.toFixed(2)}`;

              const cartCount = document.getElementById('cart-count');
              if (cartCount) {
                cartCount.textContent = result.cart_count;
              }

              if (result.cart_count === 0) {
                window.location.reload();
              }
            }
          } catch (error) {
            console.error('Error:', error);
          }
        }
      });
    });
  </script>
</body>

</html>
