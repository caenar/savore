<?php
require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/Food.php';

$food = new Food();
$allItems = $food->getAll(true);

$category = $_GET['category'] ?? null;
$filteredItems = ($category && $category !== 'all')
  ? array_filter($allItems, fn($item) => strtolower($item['category'] ?? '') === strtolower($category))
  : $allItems;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-savore-dark mb-4">Our Delicious Menu</h1>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Discover our selection of mouth-watering dishes prepared with love
        </p>
      </div>

      <div class="flex flex-wrap justify-center gap-2 mb-8">
        <a href="?category=all"
          class="<?= !$category || $category === 'all' ? 'bg-savore-primary text-white' : 'bg-white text-savore-dark' ?> px-4 py-2 rounded-full font-medium transition">
          All Items
        </a>
        <a href="?category=burger"
          class="<?= $category === 'burger' ? 'bg-savore-primary text-white' : 'bg-white text-savore-dark' ?> px-4 py-2 rounded-full font-medium transition">
          Burgers
        </a>
        <a href="?category=pizza"
          class="<?= $category === 'pizza' ? 'bg-savore-primary text-white' : 'bg-white text-savore-dark' ?> px-4 py-2 rounded-full font-medium transition">
          Pizzas
        </a>
        <a href="?category=pasta"
          class="<?= $category === 'pasta' ? 'bg-savore-primary text-white' : 'bg-white text-savore-dark' ?> px-4 py-2 rounded-full font-medium transition">
          Pastas
        </a>
        <a href="?category=drink"
          class="<?= $category === 'drink' ? 'bg-savore-primary text-white' : 'bg-white text-savore-dark' ?> px-4 py-2 rounded-full font-medium transition">
          Drinks
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php foreach ($filteredItems as $item): ?>
          <div class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition duration-300">
            <img src="https://picsum.photos/seed/food-<?= $item['id'] ?>/600/400"
              alt="<?= htmlspecialchars($item['name']) ?>"
              class="w-full h-48 object-cover">

            <div class="p-6">
              <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-bold"><?= htmlspecialchars($item['name']) ?></h3>
                <span class="text-savore-primary font-bold">$<?= number_format($item['price'], 2) ?></span>
              </div>

              <?php if (!empty($item['category'])): ?>
                <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full mb-3">
                  <?= htmlspecialchars(ucfirst($item['category'])) ?>
                </span>
              <?php endif; ?>

              <p class="text-gray-600 mb-4"><?= htmlspecialchars($item['description'] ?? '') ?></p>

              <div class="flex items-center justify-between">
                <div class="flex items-center text-yellow-400">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star<?= $i <= ($item['rating'] ?? 0) ? '' : '-alt' ?>"></i>
                  <?php endfor; ?>
                </div>

                <button class="add-to-cart bg-savore-primary hover:bg-savore-dark text-white py-2 px-4 rounded-full transition duration-300"
                  data-food-id="<?= $item['id'] ?>">
                  <i class="fas fa-plus mr-1"></i> Add
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>

  <?php include '../templates/footer.php'; ?>

  <script>
    document.querySelectorAll('.add-to-cart').forEach(button => {
      button.addEventListener('click', async (e) => {
        const foodId = e.target.closest('button').dataset.foodId;

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

            const button = e.target.closest('button');
            button.innerHTML = '<i class="fas fa-check mr-1"></i> Added';
            button.classList.add('bg-green-500', 'hover:bg-green-600');
            setTimeout(() => {
              button.innerHTML = '<i class="fas fa-plus mr-1"></i> Add';
              button.classList.remove('bg-green-500', 'hover:bg-green-600');
            }, 2000);
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
