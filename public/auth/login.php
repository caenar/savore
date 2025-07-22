<?php
require_once __DIR__ . '/../../includes/config/database.php';
require_once __DIR__ . '/../../includes/classes/User.php';

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    $error = 'Please enter both email and password!';
  } else {
    $user = new User();
    $loggedInUser = $user->login($email, $password);

    if ($loggedInUser) {
      $_SESSION['user'] = $loggedInUser;
      header('Location: /');
      exit;
    } else {
      $error = 'Invalid email or password!';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Savore</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
  <?php include '../../templates/header.php'; ?>

  <main class="min-h-screen py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
      <div class="bg-savore-primary py-4 px-6">
        <h1 class="text-white text-2xl font-bold">Welcome Back</h1>
      </div>

      <form method="POST" class="p-6">
        <?php if ($error): ?>
          <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <div class="mb-4">
          <label for="email" class="block text-gray-700 mb-2">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
        </div>

        <div class="mb-6">
          <label for="password" class="block text-gray-700 mb-2">Password</label>
          <div class="relative">
            <input type="password" id="password" name="password"
              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-savore-primary" required>
            <button type="button" class="absolute right-3 top-3 text-gray-500 toggle-password">
              <i class="far fa-eye"></i>
            </button>
          </div>
          <div class="mt-2 text-right">
            <a href="forgot_password.php" class="text-sm text-savore-primary hover:underline">Forgot password?</a>
          </div>
        </div>

        <button type="submit" class="w-full bg-savore-primary hover:bg-savore-dark text-white font-bold py-3 px-4 rounded-lg transition duration-300">
          Login
        </button>

        <div class="mt-4 text-center">
          <p class="text-gray-600">Don't have an account?
            <a href="register.php" class="text-savore-primary hover:underline">Register here</a>
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
