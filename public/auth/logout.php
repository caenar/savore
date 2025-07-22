<?php

require_once __DIR__ . '/../../includes/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

session_regenerate_id(true);
session_destroy();

header("Location: /?logout=success");
exit();
