<?php
require_once __DIR__ . '/../../../includes/config/database.php';
require_once __DIR__ . '/../../../includes/classes/Address.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/account/addresses/add.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $address = new Address();
        $isDefault = !isset($_POST['is_default']) || $_POST['is_default'] === '1';

        $data = [
          'full_name' => $_POST['full_name'],
          'phone' => $_POST['phone'],
          'address_line1' => $_POST['address_line1'],
          'address_line2' => $_POST['address_line2'] ?? '',
          'city' => $_POST['city'],
          'state' => $_POST['state'],
          'postal_code' => $_POST['postal_code'],
          'country' => $_POST['country'] ?? 'United States',
          'is_default' => $isDefault
        ];

        $address->create($userId, $data);
        header('Location: /account/addresses.php?success=Address+added+successfully');
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
  <title>Add Address | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">
  <?php include '../../../templates/header.php'; ?>

  <main class="py-12 px-4">
    <div class="container mx-auto max-w-6xl">
      <div class="flex flex-col md:flex-row gap-8">
        <?php include '../../../templates/account_sidebar.php'; ?>

        <div class="flex-1">
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
              <h2 class="text-2xl font-bold">Edit Address</h2>
            </div>

            <?php if ($error): ?>
              <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-6 mt-6">
                <p><?= htmlspecialchars($error) ?></p>
              </div>
            <?php endif; ?>

            <form method="POST" class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                  <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                  <input type="text" id="full_name" name="full_name"
                    value="<?= htmlspecialchars($currentAddress['full_name']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary" required>
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                  <input type="tel" id="phone" name="phone"
                    value="<?= htmlspecialchars($currentAddress['phone']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary" required>
                </div>

                <div class="md:col-span-2">
                  <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                  <input type="text" id="address_line1" name="address_line1"
                    value="<?= htmlspecialchars($currentAddress['address_line1']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary" required>
                </div>

                <div class="md:col-span-2">
                  <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                  <input type="text" id="address_line2" name="address_line2"
                    value="<?= htmlspecialchars($currentAddress['address_line2']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary">
                </div>

                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                  <input type="text" id="city" name="city"
                    value="<?= htmlspecialchars($currentAddress['city']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary" required>
                </div>

                <div>
                  <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                  <input type="text" id="state" name="state"
                    value="<?= htmlspecialchars($currentAddress['state']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary" required>
                </div>

                <div>
                  <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                  <input type="text" id="postal_code" name="postal_code"
                    value="<?= htmlspecialchars($currentAddress['postal_code']) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary" required>
                </div>

                <div>
                  <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                  <select id="country" name="country"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary">
                    <option value="United States" <?= $currentAddress['country'] === 'United States' ? 'selected' : '' ?>>United States</option>
                    <option value="Canada" <?= $currentAddress['country'] === 'Canada' ? 'selected' : '' ?>>Canada</option>
                    <!-- Add more countries as needed -->
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="inline-flex items-center">
                    <input type="checkbox" name="is_default" value="1"
                      class="rounded border-gray-300 text-savore-primary focus:ring-savore-primary"
                      <?= $currentAddress['is_default'] ? 'checked' : '' ?>>
                    <span class="ml-2">Set as default shipping address</span>
                  </label>
                </div>
              </div>

              <div class="mt-8 flex justify-end gap-4">
                <a href="/account/addresses.php" class="border border-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg transition">
                  Cancel
                </a>
                <button type="submit" class="bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-lg transition">
                  Add address
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../../../templates/footer.php'; ?>
</body>

</html>
