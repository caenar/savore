<?php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>

<aside class="w-full md:w-64 flex-shrink-0">
  <div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center gap-4 mb-6">
      <div class="bg-savore-primary text-white w-12 h-12 rounded-full flex items-center justify-center">
        <i class="fas fa-user text-xl"></i>
      </div>
      <div>
        <h2 class="font-bold"><?= htmlspecialchars($_SESSION['user']['username']) ?></h2>
        <p class="text-sm text-gray-600">Member since <?= date('M Y', strtotime($_SESSION['user']['created_at'])) ?></p>
      </div>
    </div>

    <nav class="space-y-2">
      <a href="/account.php"
        class="block px-4 py-2 <?= $currentPage === 'account.php' ? 'bg-savore-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?> rounded-lg transition">
        <i class="fas fa-user-circle mr-2"></i> Account Overview
      </a>
      <a href="/account/orders.php"
        class="block px-4 py-2 <?= str_contains($currentPage, 'orders.php') ? 'bg-savore-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?> rounded-lg transition">
        <i class="fas fa-receipt mr-2"></i> My Orders
      </a>
      <a href="/account/addresses.php"
        class="block px-4 py-2 <?= $currentPage === 'addresses.php' ? 'bg-savore-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?> rounded-lg transition">
        <i class="fas fa-map-marker-alt mr-2"></i> Addresses
      </a>
      <a href="/account/settings.php"
        class="block px-4 py-2 <?= $currentPage === 'settings.php' ? 'bg-savore-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?> rounded-lg transition">
        <i class="fas fa-cog mr-2"></i> Settings
      </a>
      <a href="/auth/logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
        <i class="fas fa-sign-out-alt mr-2"></i> Logout
      </a>
    </nav>
  </div>
</aside>
