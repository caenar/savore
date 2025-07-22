<?php
require_once __DIR__ . "/../includes/classes/Cart.php";

session_start();
$isLoggedIn = isset($_SESSION['user']);
$user = $_SESSION['user'] ?? null;

$cartCount = 0;
$cartItems = [];
if ($isLoggedIn) {
    $cart = new Cart();
    $cartId = $cart->getUserCart($user['id']);
    $cartItems = $cart->getContents($cartId);
    $cartCount = count($cartItems);
}
?>

<head>
  <link rel="stylesheet" href="/style.css">
  <script src="/main.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            savore: {
              primary: '#ff6b6b',
              secondary: '#4ecdc4',
              dark: '#292f36',
              light: '#f7fff7',
            }
          }
        }
      }
    }
  </script>
</head>

<body class="bg-gray-50">
  <header class="bg-savore-dark text-savore-light shadow-lg">
    <div class="container mx-auto px-4 py-6">
      <div class="flex justify-between items-center">
        <a href="/" class="text-2xl font-bold text-savore-primary">
          Savore
        </a>

        <nav class="hidden md:flex items-center space-x-8">
          <a href="/menu.php" class="hover:text-savore-primary transition">Menu</a>

          <div class="relative group py-2">
            <a href="/cart.php" class="hover:text-savore-primary transition flex items-center">
              Cart
              <span id="cart-count" class="bg-savore-primary text-white text-xs px-2 py-1 rounded-full ml-1">
                <?= $cartCount ?>
              </span>
            </a>

            <?php if ($cartCount > 0): ?>
              <div class="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right transform scale-95 group-hover:scale-100">
                <div class="p-4 max-h-96 overflow-y-auto">
                  <h3 class="font-bold text-savore-dark mb-2">Your Cart</h3>
                  <div class="divide-y divide-gray-200">
                    <?php foreach ($cartItems as $item): ?>
                      <div class="py-3 flex items-center space-x-3">
                        <img src="https://picsum.photos/seed/food-<?= $item['food_id'] ?>/100/100"
                          alt="<?= htmlspecialchars($item['name']) ?>"
                          class="w-12 h-12 object-cover rounded">
                        <div class="flex-1">
                          <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
                          <p class="text-xs text-gray-500"><?= $item['quantity'] ?> x $<?= number_format($item['price'], 2) ?></p>
                        </div>
                        <p class="text-sm font-semibold">$<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="border-t border-gray-200 p-4 bg-gray-50">
                  <div class="flex justify-between items-center mb-3">
                    <span class="font-semibold text-sm">Subtotal</span>
                    <span class="font-bold text-savore-primary">
                      $<?= number_format(array_reduce($cartItems, function ($sum, $item) {
                          return $sum + ($item['price'] * $item['quantity']);
                      }, 0), 2) ?>
                    </span>
                  </div>
                  <a href="/cart.php" class="block w-full bg-savore-primary hover:bg-savore-dark text-white text-center py-2 px-4 rounded-md transition">
                    View Cart
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <?php if ($isLoggedIn): ?>
            <div class="relative group py-2">
              <button class="flex items-center space-x-2 focus:outline-none">
                <span class="hover:text-savore-primary transition">
                  <?= htmlspecialchars($user['username']) ?>
                </span>
                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              <div class="absolute right-0 mt-0 w-48 bg-white rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right transform scale-95 group-hover:scale-100">
                <a href="/account.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-savore-primary transition">Account</a>
                <?php if ($user['role_id'] == 2): ?>
                  <a href="/admin/" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-savore-primary transition">Admin Panel</a>
                <?php endif; ?>
                <a href="/auth/logout.php" class="block px-4 py-2 text-gray-800 hover:bg-gray-100 hover:text-savore-primary transition">Logout</a>
              </div>
            </div>
          <?php else: ?>
            <a href="/auth/login.php" class="hover:text-savore-primary transition">Login</a>
            <a href="/auth/register.php" class="bg-savore-primary hover:bg-savore-dark text-white px-4 py-2 rounded-full transition duration-300">
              Register
            </a>
          <?php endif; ?>
        </nav>

        <button class="md:hidden focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>
  </header>
