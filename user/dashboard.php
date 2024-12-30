<?php
session_start();
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

// Logika Pencarian Produk & Kategori
$search_query = '';
$category_filter = '';

if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

if (isset($_GET['category'])) {
    $category_filter = trim($_GET['category']);
}

// Query produk dengan filter pencarian dan kategori
$query = "SELECT product_id, name, image, price FROM products";
$conditions = [];
$params = [];
$types = "";

// Filter kategori
if (!empty($category_filter)) {
    $conditions[] = "category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

// Filter pencarian
if (!empty($search_query)) {
    $conditions[] = "name LIKE ?";
    $params[] = "%" . $search_query . "%";
    $types .= "s";
}

// Gabungkan kondisi SQL
if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$product_result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopNest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @media (max-width: 576px) {
  header .d-flex {
    margin-top: 10px;
  }
}

    </style>
</head>
<body>

<header class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
  <div class="container">
    <a class="navbar-brand" href="#">ShopNest</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <form class="d-flex" method="GET" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
          </form>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['full_name']); ?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="riwayat.php">Riwayat Pesanan</a></li>
            <li><a class="dropdown-item" href="profile_settings.php">Akun Saya</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../index.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</header>

<div class="bg-light text-center py-5">
  <div class="container">
    <h1 class="display-4">Temukan Produk Favoritmu!</h1>
    <p class="lead">Diskon hingga 50% untuk berbagai produk pilihan.</p>
  </div>
</div>

<section class="container my-5">
  <h2 class="text-center mb-4"> <?php echo !empty($category_filter) ? "Kategori " . htmlspecialchars($category_filter) : "Rekomendasi"; ?></h2>
  <div class="row g-4">
    <?php
    if ($product_result->num_rows > 0) {
        while ($row = $product_result->fetch_assoc()) {
            $product_id = $row['product_id'];
            echo "<div class='col-md-3'>";
            echo "<div class='card'>";
            echo "<img src='../assets/uploads/" . htmlspecialchars(basename($row['image'])) . "' class='card-img-top' alt='" . htmlspecialchars($row['name']) . "'>";
            echo "<div class='card-body'>";
            echo "<h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>";
            echo "<p class='card-text'>Rp " . number_format($row['price'], 0, ',', '.') . "</p>";
            echo "<a href='checkout-page.php?id=$product_id' class='btn btn-primary'>Beli</a>";
            echo "</div></div></div>";
        }
    } else {
        echo "<p class='text-center'>Tidak ada produk yang tersedia untuk kategori ini.</p>";
    }
    ?>
  </div>
</section>


<footer class="bg-dark text-white text-center py-4">
  <p>&copy; <?php echo date("Y"); ?> ShopNest | Semua Hak Cipta Dilindungi</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html> 
