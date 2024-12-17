<?php
// session_start();
include('../includes/db.php'); // Koneksi database

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Mendapatkan data pengguna
$user_id = $_SESSION['user_id'];
$query = "SELECT full_name, email, phone, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    /* Reset */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
      }

      body {
        background-color: #f3f4f6;
        color: #333;
        line-height: 1.6;
      }

      /* Sticky Header */
      header {
        position: sticky; /* Membuat header tetap menempel */
        top: 0; /* Header berada di bagian atas layar */
        z-index: 1000; /* Header akan tampil di atas elemen lain */
        background-color: #2d2f3b; /* Menjaga warna background */
        color: white; /* Menjaga warna teks */
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Efek bayangan */
        margin-bottom: 15px;
      }


      header .logo {
        font-size: 1.8rem;
        font-weight: bold;
        color: #00a8e8;
      }

      header .search-bar {
        display: flex;
        align-items: center;
        width: 50%;
      }

      header .search-bar input {
        /* width: 100%; */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-right: 10px;
      }

      header .search-bar button {
        background-color: #00a8e8;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
      }

      header .user-menu {
        display: flex;
        align-items: center;
        gap: 15px;
      }

      header .user-menu a {
        text-decoration: none;
        font-weight: bold;
      }

      header .user-menu i {
        font-size: 20px;
        /* cursor: pointer; */
      }

      header .user-menu p {
        margin: 0;
      }


      /* User Menu */
      .user-menu {
        position: relative;
        cursor: pointer;
      }

      .user-menu:hover .dropdown-menu {
        display: block;
      }

      .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        z-index: 1000;
        width: 150px;
        text-align: left;
      }

      .dropdown-menu a {
        display: block;
        padding: 10px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        border-bottom: 1px solid #f4f4f9;
      }

      .dropdown-menu a:last-child {
        border-bottom: none;
      }

      .dropdown-menu a:hover {
        background: #f4f4f9;
        color: #27ae60;
      }
  </style>
</head>
<body>
<header>
  <div class="logo">ShopNest</div>
  <div class="user-menu">
    <i class="fas fa-user-circle"></i>
    <p class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></p>
    
    <div class="dropdown-menu">
      <a href="dashboard.php">Beranda</a>
      <a href="riwayat.php">Riwayat Pesanan</a>
      <a href="profile_settings.php">Akun Saya</a>
      <a href="../index.php">Logout</a>
    </div>
  </div>
</header> 
</body>
</html>