<?php

// public/account/addresses/set_default.php
require_once __DIR__ . '/../../../includes/config/database.php';
require_once __DIR__ . '/../../../includes/classes/Address.php';

session_start();

if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /auth/login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$addressId = $_POST['address_id'] ?? null;

if ($addressId) {
    $address = new Address();
    try {
        $address->setDefault($addressId, $userId);
        header('Location: /account/addresses.php?success=Default+address+updated');
    } catch (Exception $e) {
        header('Location: /account/addresses.php?error=' . urlencode($e->getMessage()));
    }
} else {
    header('Location: /account/addresses.php?error=Invalid+address');
}
exit;
