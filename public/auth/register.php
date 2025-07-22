<?php
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (empty($username) || empty($email) || empty($password)) {
    $error = 'All fields are required!';
  } elseif ($password !== $confirm_password) {
    $error = 'Passwords do not match!';
  } else {
    try {
      $user = new User();
      if ($user->register($username, $email, $password)) {
        $success = 'Registration successful! Please login.';
        $username = $email = '';
      }
    } catch (Exception $e) {
      $error = $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
  <?php include '../../templates/header.php'; ?>

  <main class="min-h-screen py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-savore-primary py-4 px-6">
        <h1 class="text-white text-2xl font-bold">Create Account</h1>
      </div>

      <form method="POST" class="p-6">
        <?php if ($error): ?>
          <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
          </div>
        <?php endif; ?>

        <div class="mb-4">
          <label for="username" class="block text-gray-700 mb-2">Username</label>
          <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
        </div>

        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-2">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
        </div>

        <div class="mb-4">
          <label for="password" class="block text-gray-700 mb-2">Password</label>
          <div class="relative">
            <input type="password" id="password" name="password"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
            <button type="button" class="absolute right-3 top-3 text-gray-500 toggle-password">
              <i class="far fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="mb-6">
          <label for="confirm_password" class="block text-gray-700 mb-2">Confirm Password</label>
          <div class="relative">
            <input type="password" id="confirm_password" name="confirm_password"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
            <button type="button" class="absolute right-3 top-3 text-gray-500 toggle-password">
              <i class="far fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="w-full bg-savore-primary hover:bg-savore-dark text-white font-bold py-3 px-4 rounded-lg transition duration-300">
          Register
        </button>

        <div class="mt-4 text-center">
          <p class="text-gray-600">Already have an account?
            <a href="login.php" class="text-savore-primary hover:underline">Login here</a>
          </p>
        </div>
      </form>
    </div>
  </main>

  <?php include '../../templates/footer.php'; ?>

  <script>
    document.querySelectorAll('.toggle-password').forEach(button => {
      button.addEventListener('click', (e) => {
        const input = e.target.closest('.relative').querySelector('input');
        const icon = e.target.closest('button').querySelector('i');

        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
          input.type = 'password';
          icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
      });
    });
  </script>
</body>

</html>
