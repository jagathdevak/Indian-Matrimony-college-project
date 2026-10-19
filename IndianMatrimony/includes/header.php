<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' • Vivah' : 'Vivah • Find Your Forever' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <a class="brand" href="index.php"><span class="brand-mark">♥</span><span>vivah</span></a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="profiles.php">Discover</a>
    <a href="about.php">How it works</a>
    <?php if (isAdminLoggedIn()): ?>
      <a href="admin.php">Admin Dashboard</a>
      <a class="nav-cta" href="admin_logout.php">Admin Logout</a>
    <?php elseif (isLoggedIn()): ?>
      <a href="dashboard.php">My Matches</a>
      <a class="nav-cta" href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a class="nav-cta" href="register.php">Create Profile</a>
    <?php endif; ?>
  </div>
</nav>
<main>