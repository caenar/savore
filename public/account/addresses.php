<?php
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/User.php';
require_once __DIR__ . '/../../includes/classes/Address.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/account/addresses.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$address = new Address();
$addresses = $address->getByUser($userId);

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Addresses | Savore</title>
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
            <div class="p-6 border-b border-gray-200">
              <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">My Addresses</h2>
                <a href="/account/addresses/add.php"
                  class="bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-4 rounded-lg transition">
                  Add New Address
                </a>
              </div>
            </div>

            <?php if ($success): ?>
              <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mx-6 mt-6">
                <p><?= htmlspecialchars($success) ?></p>
              </div>
            <?php endif; ?>

            <?php if ($error): ?>
              <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-6 mt-6">
                <p><?= htmlspecialchars($error) ?></p>
              </div>
            <?php endif; ?>

            <div class="p-6">
              <?php if (empty($addresses)): ?>
                <div class="text-center py-8">
                  <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                  <p class="text-gray-600">You haven't saved any addresses yet</p>
                  <a href="/account/addresses/add.php"
                    class="inline-block mt-4 bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-full transition">
                    Add Your First Address
                  </a>
                </div>
              <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <?php foreach ($addresses as $addr): ?>
                    <div class="border border-gray-200 rounded-lg p-6 <?= $addr['is_default'] ? 'border-savore-primary' : '' ?>">
                      <?php if ($addr['is_default']): ?>
                        <span class="bg-savore-primary text-white text-xs px-2 py-1 rounded-full mb-2 inline-block">
                          Default
                        </span>
                      <?php endif; ?>

                      <h3 class="font-bold"><?= htmlspecialchars($addr['full_name']) ?></h3>
                      <p class="text-gray-600"><?= htmlspecialchars($addr['phone']) ?></p>

                      <div class="mt-4">
                        <p><?= htmlspecialchars($addr['address_line1']) ?></p>
                        <?php if (!empty($addr['address_line2'])): ?>
                          <p><?= htmlspecialchars($addr['address_line2']) ?></p>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?> <?= htmlspecialchars($addr['postal_code']) ?></p>
                        <p><?= htmlspecialchars($addr['country']) ?></p>
                      </div>

                      <div class="mt-4 flex gap-2">
                        <a href="/account/addresses/edit.php?id=<?= $addr['id'] ?>"
                          class="text-sm text-savore-primary hover:underline">
                          Edit
                        </a>
                        <?php if (!$addr['is_default']): ?>
                          <form action="/account/addresses/set_default.php" method="POST" class="inline">
                            <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                            <button type="submit" class="text-sm text-savore-primary hover:underline">
                              Set as Default
                            </button>
                          </form>
                        <?php endif; ?>
                        <form action="/account/addresses/delete.php" method="POST" class="inline">
                          <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                          <button type="submit" class="text-sm text-red-500 hover:underline"
                            onclick="return confirm('Delete this address?')">
                            Delete
                          </button>
                        </form>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../../templates/footer.php'; ?>
</body>

</html>
