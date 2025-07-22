<?php
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/User.php';
require_once __DIR__ . '/../../includes/classes/Order.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/account/orders.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$order = new Order();
$orderHistory = $order->getHistory($userId);

// If viewing specific order
$orderId = $_GET['id'] ?? null;
$orderDetails = $orderId ? $order->getDetails($orderId) : null;

if ($orderId && (!$orderDetails || $orderDetails['user_id'] != $userId)) {
    header('Location: /account/orders.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $orderId ? 'Order Details' : 'My Orders' ?> | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto max-w-6xl">
      <div class="flex flex-col md:flex-row gap-8">
        <?php include '../../templates/account_sidebar.php'; ?>

        <div class="flex-1">
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <?php if ($orderId && $orderDetails): ?>
              <!-- Order Details View -->
              <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-start">
                  <div>
                    <h2 class="text-2xl font-bold">Order #<?= $orderDetails['id'] ?></h2>
                    <p class="text-gray-600">Placed on <?= date('F j, Y \a\t g:i A', strtotime($orderDetails['created_at'])) ?></p>
                  </div>
                  <span class="px-3 py-1 text-sm rounded-full 
                                        <?= $orderDetails['status'] === 'completed' ? 'bg-green-100 text-green-800' : ($orderDetails['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                    <?= ucfirst($orderDetails['status']) ?>
                  </span>
                </div>
              </div>

              <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold mb-4">Order Items</h3>
                <div class="divide-y divide-gray-200">
                  <?php foreach ($orderDetails['items'] as $item): ?>
                    <div class="py-4 flex justify-between">
                      <div class="flex items-start gap-4">
                        <img src="https://picsum.photos/seed/food-<?= $item['food_id'] ?>/100/100"
                          alt="<?= htmlspecialchars($item['food_name']) ?>"
                          class="w-16 h-16 object-cover rounded">
                        <div>
                          <h4 class="font-medium"><?= htmlspecialchars($item['food_name']) ?></h4>
                          <p class="text-gray-600 text-sm">Qty: <?= $item['quantity'] ?></p>
                        </div>
                      </div>
                      <div class="text-right">
                        <p>$<?= number_format($item['price_at_order'], 2) ?></p>
                        <p class="text-gray-600 text-sm">$<?= number_format($item['price_at_order'] * $item['quantity'], 2) ?></p>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                  <h3 class="text-lg font-semibold mb-4">Delivery Information</h3>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="font-medium">Standard Delivery</p>
                    <p class="text-gray-600">Estimated delivery: <?= date('F j', strtotime($orderDetails['created_at'] . ' + 3 days')) ?></p>
                  </div>
                </div>

                <div>
                  <h3 class="text-lg font-semibold mb-4">Order Summary</h3>
                  <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between py-2 border-b border-gray-200">
                      <span>Subtotal</span>
                      <span>$<?= number_format($orderDetails['total'] - 3.99, 2) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-200">
                      <span>Delivery</span>
                      <span>$3.99</span>
                    </div>
                    <div class="flex justify-between py-2 font-bold">
                      <span>Total</span>
                      <span>$<?= number_format($orderDetails['total'], 2) ?></span>
                    </div>
                    <?php if ($orderDetails['payment_method']): ?>
                      <div class="mt-4 pt-4 border-t border-gray-200 text-sm">
                        <p>Paid via <?= ucfirst($orderDetails['payment_method']) ?></p>
                        <?php if ($orderDetails['transaction_id']): ?>
                          <p class="text-gray-600">Transaction: <?= $orderDetails['transaction_id'] ?></p>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

            <?php else: ?>
              <!-- Order List View -->
              <div class="p-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold">My Orders</h2>
              </div>

              <?php if (empty($orderHistory)): ?>
                <div class="p-12 text-center">
                  <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                  <p class="text-gray-600 mb-6">You haven't placed any orders yet</p>
                  <a href="/menu.php" class="inline-block bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-full transition">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                      <?php foreach ($orderHistory as $order): ?>
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $order['id'] ?></td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= count($order['items'] ?? []) ?></td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($order['total'], 2) ?></td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full 
                                                        <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                              <?= ucfirst($order['status']) ?>
                            </span>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/account/orders.php?id=<?= $order['id'] ?>" class="text-savore-primary hover:underline">View Details</a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../../templates/footer.php'; ?>
</body>

</html>
