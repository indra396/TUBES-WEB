<?php
// session_start();
include('../includes/db.php'); // Koneksi database

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
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
  <title>ShopNest</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>  
    /* Sembunyikan tanda dropdown di tampilan mobile */
    @media (max-width: 768px) {
      .user-details{
        display: none; /* Sembunyikan logo dan nama pengguna */
      }
      .dropdown-menu {
        display: block; /* Pastikan dropdown tetap terlihat */
        position: static; /* Tampilkan dalam tata letak biasa */
        box-shadow: none; /* Hilangkan bayangan */
      }
      span{
        display: none;
      }

    }
    .nav-link.dropdown-toggle::after {
        display: none; /* Menyembunyikan tanda segitiga */
    }
  </style>
</head> 
<body>
<header class="navbar navbar-expand-lg bg-light sticky-top p-3 shadow-sm">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand fw-bold" href="#">ShopNest</a>

      <!-- Toggle button for mobile view -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Collapsible content -->
      <div class="collapse navbar-collapse" id="navbarContent">
        <!-- User Menu -->
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center user-details" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span><i class="fas fa-user-circle me-2"></i></span>
              <span><?php echo htmlspecialchars($user['full_name']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
              <li><a class="dropdown-item" href="dashboard.php">Home</a></li>              
              <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
              <li><a class="dropdown-item" href="profile_settings.php">Akun Saya</a></li>
              <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </header>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
