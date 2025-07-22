<?php
require_once __DIR__ . '/../includes/config/database.php';
require_once __DIR__ . '/../includes/classes/Order.php';

session_start();

if (!isset($_GET['id'])) {
    header('Location: /');
    exit;
}

$orderId = $_GET['id'];
$order = new Order();
$orderDetails = $order->getDetails($orderId);

if ($orderDetails['user_id'] !== ($_SESSION['user']['id'] ?? null)) {
    header('Location: /');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto max-w-2xl">
      <div class="bg-white rounded-lg shadow overflow-hidden text-center">
        <div class="bg-green-100 p-6">
          <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
          <h1 class="text-2xl font-bold text-gray-800">Order Confirmed!</h1>
          <p class="text-gray-600">Thank you for your purchase</p>
        </div>

        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Order Number</span>
            <span class="font-medium">#<?= $orderId ?></span>
          </div>
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600">Date</span>
            <span class="font-medium"><?= date('F j, Y', strtotime($orderDetails['created_at'])) ?></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Total</span>
            <span class="font-bold text-lg">$<?= number_format($orderDetails['total'], 2) ?></span>
          </div>
        </div>

        <div class="p-6">
          <h2 class="text-lg font-semibold mb-4">What's Next?</h2>
          <p class="text-gray-600 mb-6">We've sent an order confirmation to your email. Your food will be prepared and delivered soon.</p>

          <a href="/menu.php" class="inline-block bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-full transition">
            Back to Menu
          </a>
        </div>
      </div>
    </div>
  </main>

  <?php include '../templates/footer.php'; ?>
</body>

</html>
