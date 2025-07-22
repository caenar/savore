<?php
require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/User.php';
require_once __DIR__ . '/../includes/classes/Order.php';
require_once __DIR__ . '/../includes/classes/Address.php';

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/account.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$user = new User();
$order = new Order();

$userDetails = $user->getById($userId);
$orderHistory = $order->getHistory($userId);

$address = new Address();
$defaultAddress = $address->getDefault($userId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto max-w-6xl">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0">
          <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-4 mb-6">
              <div class="bg-savore-primary text-white w-12 h-12 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-xl"></i>
              </div>
              <div>
                <h2 class="font-bold"><?= htmlspecialchars($userDetails['username']) ?></h2>
                <p class="text-sm text-gray-600">Member since <?= date('M Y', strtotime($userDetails['created_at'])) ?></p>
              </div>
            </div>

            <nav class="space-y-2">
              <a href="/account.php" class="block px-4 py-2 bg-savore-primary text-white rounded-lg font-medium">
                <i class="fas fa-user-circle mr-2"></i> Account Overview
              </a>
              <a href="/account/orders.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-receipt mr-2"></i> My Orders
              </a>
              <a href="/account/addresses.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-map-marker-alt mr-2"></i> Addresses
              </a>
              <a href="/account/settings.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-cog mr-2"></i> Settings
              </a>
              <a href="/auth/logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
              </a>
            </nav>
          </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Account Overview -->
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-2xl font-bold mb-6">Account Overview</h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="border border-gray-200 rounded-lg p-4">
                  <h3 class="font-semibold mb-2 flex items-center gap-2">
                    <i class="fas fa-user text-savore-primary"></i>
                    Personal Information
                  </h3>
                  <p class="text-gray-700"><?= htmlspecialchars($userDetails['username']) ?></p>
                  <p class="text-gray-700"><?= htmlspecialchars($userDetails['email']) ?></p>
                  <a href="/account/settings.php" class="inline-block mt-3 text-sm text-savore-primary hover:underline">
                    Edit Profile
                  </a>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                  <h3 class="font-semibold mb-2 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-savore-primary"></i>
                    Default Address
                  </h3>

                  <?php if ($defaultAddress): ?>
                    <address class="not-italic text-gray-700">
                      <strong><?= htmlspecialchars($defaultAddress['full_name']) ?></strong><br>
                      <?= htmlspecialchars($defaultAddress['address_line1']) ?><br>
                      <?php if (!empty($defaultAddress['address_line2'])): ?>
                        <?= htmlspecialchars($defaultAddress['address_line2']) ?><br>
                      <?php endif; ?>
                      <?= htmlspecialchars($defaultAddress['city']) ?>,
                      <?= htmlspecialchars($defaultAddress['state']) ?>
                      <?= htmlspecialchars($defaultAddress['postal_code']) ?><br>
                      <?= htmlspecialchars($defaultAddress['phone']) ?>
                    </address>
                    <div class="mt-2">
                      <a href="/account/addresses.php" class="text-sm text-savore-primary hover:underline">
                        Manage Addresses
                      </a>
                    </div>
                  <?php else: ?>
                    <p class="text-gray-600 italic">No address saved</p>
                    <a href="/account/addresses.php" class="inline-block mt-2 text-sm text-savore-primary hover:underline">
                      Add Address
                    </a>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Recent Orders -->
              <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                  <h2 class="text-xl font-bold">Recent Orders</h2>
                  <a href="/account/orders.php" class="text-sm text-savore-primary hover:underline">
                    View All Orders
                  </a>
                </div>

                <?php if (empty($orderHistory)): ?>
                  <div class="text-center py-8">
                    <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600">You haven't placed any orders yet</p>
                    <a href="/menu.php" class="inline-block mt-4 bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-full transition">
                      Start Ordering
                    </a>
                  </div>
                <?php else: ?>
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach (array_slice($orderHistory, 0, 3) as $order): ?>
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $order['id'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($order['total'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 py-1 text-xs rounded-full 
                                                        <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                <?= ucfirst($order['status']) ?>
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <a href="/account/orders.php?id=<?= $order['id'] ?>" class="text-savore-primary hover:underline">View</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
  </main>

  <?php include '../templates/footer.php'; ?>
</body>

</html>
