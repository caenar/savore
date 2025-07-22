<?php
require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/Food.php';

$food = new Food();
$featuredItems = $food->getAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Savore - Delicious Food Delivered</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .hero-bg {
      background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../assets/images/hero-bg.jpg');
      background-size: cover;
      background-position: center;
    }

    .food-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>

<body class="bg-gray-50">
  <?php include '../templates/header.php'; ?>

  <section class="hero-bg text-white py-32 px-4">
    <div class="container mx-auto text-center">
      <h1 class="text-4xl md:text-6xl font-bold mb-6">Hungry? You're in the right place</h1>
      <p class="text-xl mb-8 max-w-2xl mx-auto">Delicious meals delivered to your door in under 30 minutes</p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/menu.php" class="bg-savore-primary hover:bg-red-600 text-white font-bold py-3 px-8 rounded-full transition duration-300">
          Order Now
        </a>
        <a href="#menu" class="bg-white hover:bg-gray-100 text-savore-dark font-bold py-3 px-8 rounded-full transition duration-300">
          Browse Menu
        </a>
      </div>
    </div>
  </section>

  <section class="py-16 bg-white">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-12">How Savore Works</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center p-6">
          <div class="bg-savore-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-utensils text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">1. Choose Your Food</h3>
          <p class="text-gray-600">Browse our delicious menu and select your favorites</p>
        </div>

        <div class="text-center p-6">
          <div class="bg-savore-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-cash-register text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">2. Place Your Order</h3>
          <p class="text-gray-600">Checkout securely with our simple payment system</p>
        </div>

        <div class="text-center p-6">
          <div class="bg-savore-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-motorcycle text-2xl"></i>
          </div>
          <h3 class="text-xl font-bold mb-2">3. Fast Delivery</h3>
          <p class="text-gray-600">Sit back while we prepare and deliver your meal</p>
        </div>
      </div>
    </div>
  </section>

  <section id="menu" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-4">Our Featured Menu</h2>
      <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">Check out some of our customer favorites</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($featuredItems as $item): ?>
          <div class="food-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
            <img src="/assets/images/food/<?= htmlspecialchars($item['image_url'] ?? 'default.jpg') ?>"
              alt="<?= htmlspecialchars($item['name']) ?>"
              class="w-full h-48 object-cover">
            <div class="p-6">
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h3>
                <span class="text-savore-primary font-bold">$<?= number_format($item['price'], 2) ?></span>
              </div>
              <p class="text-gray-600 mb-4"><?= htmlspecialchars($item['description'] ?? '') ?></p>
              <button class="add-to-cart bg-savore-primary hover:bg-red-600 text-white py-2 px-4 rounded-full transition duration-300 w-full"
                data-food-id="<?= $item['id'] ?>">
                Add to Cart
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="text-center mt-12">
        <a href="/menu.php" class="inline-block border-2 border-savore-primary text-savore-primary hover:bg-savore-primary hover:text-white font-bold py-3 px-8 rounded-full transition duration-300">
          View Full Menu
        </a>
      </div>
    </div>
  </section>

  <section class="py-16 bg-white">
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-12">What Our Customers Say</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-gray-50 p-6 rounded-lg">
          <div class="flex items-center mb-4">
            <div class="bg-savore-primary text-white w-10 h-10 rounded-full flex items-center justify-center mr-4">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h4 class="font-bold">Alex Johnson</h4>
              <div class="flex text-yellow-400">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
            </div>
          </div>
          <p class="text-gray-600">"The spicy burger is my absolute favorite! Delivery is always faster than promised."</p>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg">
          <div class="flex items-center mb-4">
            <div class="bg-savore-primary text-white w-10 h-10 rounded-full flex items-center justify-center mr-4">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h4 class="font-bold">Maria Garcia</h4>
              <div class="flex text-yellow-400">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
              </div>
            </div>
          </div>
          <p class="text-gray-600">"I order from Savore at least twice a week. Never had a bad experience!"</p>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg">
          <div class="flex items-center mb-4">
            <div class="bg-savore-primary text-white w-10 h-10 rounded-full flex items-center justify-center mr-4">
              <i class="fas fa-user"></i>
            </div>
            <div>
              <h4 class="font-bold">Sam Wilson</h4>
              <div class="flex text-yellow-400">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
              </div>
            </div>
          </div>
          <p class="text-gray-600">"The truffle fries are to die for! Perfect for late night cravings."</p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-16 bg-savore-primary text-white">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-6">Ready to order your favorite meal?</h2>
      <p class="text-xl mb-8 max-w-2xl mx-auto">Join thousands of satisfied customers enjoying delicious food</p>
      <a href="/menu.php" class="inline-block bg-white text-savore-primary hover:bg-gray-100 font-bold py-3 px-8 rounded-full transition duration-300">
        Start Ordering Now
      </a>
    </div>
  </section>

  <?php include '../templates/footer.php'; ?>

  <script>
    document.querySelectorAll('.add-to-cart').forEach(button => {
      button.addEventListener('click', async (e) => {
        const foodId = e.target.dataset.foodId;

        try {
          const response = await fetch('/cart/add.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              food_id: foodId,
              quantity: 1
            })
          });

          const result = await response.json();

          if (result.success) {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
              cartCount.textContent = result.cart_count;
            }

            alert('Item added to cart!');
          } else {
            alert('Please login to add items to cart');
            window.location.href = '/auth/login.php';
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Failed to add item to cart');
        }
      });
    });
  </script>
</body>

</html>
