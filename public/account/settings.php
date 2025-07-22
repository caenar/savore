<?php
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/User.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php?redirect=/account/settings.php');
    exit;
}

$database = new Database();
$db = $database->getInstance();

$userId = $_SESSION['user']['id'];
$user = new User();
$userDetails = $user->getById($userId);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    try {
        if (empty($username) || empty($email)) {
            throw new Exception('Username and email are required');
        }

        $user->update($userId, [
          'username' => $username,
          'email' => $email
        ]);

        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                throw new Exception('Current password is required to change password');
            }

            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $dbPassword = $stmt->fetchColumn();

            if (!password_verify($currentPassword, $dbPassword)) {
                throw new Exception('Current password is incorrect');
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $user->update($userId, ['password' => $hashedPassword]);
        }

        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email'] = $email;
        $userDetails = $user->getById($userId);

        $success = 'Account updated successfully!';
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
  <title>Account Settings | Savore</title>
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
              <h2 class="text-2xl font-bold">Account Settings</h2>
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

            <form method="POST" class="p-6">
              <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4">Profile Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($userDetails['username']) ?>"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary">
                  </div>
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($userDetails['email']) ?>"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary">
                  </div>
                </div>
              </div>

              <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4">Change Password</h3>
                <div class="space-y-4">
                  <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary">
                  </div>
                  <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" id="new_password" name="new_password"
                      class="w-full px-4 py-2 border rounded-lg focus:ring-savore-primary focus:border-savore-primary">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button type="submit" class="bg-savore-primary hover:bg-savore-dark text-white font-bold py-2 px-6 rounded-lg transition">
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include '../../templates/footer.php'; ?>
</body>

</html>
