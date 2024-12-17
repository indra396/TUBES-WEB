<?php
session_start();
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/dashboard_users.css">
</head>
<body>

<header>
  <div class="logo">ShopNest</div>

  <div class="search-bar">
    <form method="GET" action="">
      <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search_query); ?>">
      <button type="submit">Cari</button>
    </form>
  </div>

  <div class="user-menu">
    <i class="fas fa-user-circle"></i>
    <p class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></p>
    <div class="dropdown-menu">
      <a href="riwayat.php">Riwayat Pesanan</a>
      <a href="profile_settings.php">Akun Saya</a>
      <a href="../index.php">Logout</a>
    </div>
  </div>
</header>

<div class="hero">
  <h1>Temukan Produk Favoritmu!</h1>
  <p>Diskon hingga 50% untuk berbagai produk pilihan.</p>
</div>

<section class="categories">
  <h2>Kategori </h2>
  <div class="categories-grid">
    <a href="?category=Elektronik" class="category-card">
      <i class="fas fa-laptop"></i><p>Elektronik</p>
    </a>
    <a href="?category=Fashion" class="category-card">
      <i class="fas fa-tshirt"></i><p>Fashion</p>
    </a>
    <a href="?category=Kesehatan" class="category-card">
      <i class="fas fa-heart"></i><p>Kesehatan</p>
    </a>
    <a href="?category=Perabot" class="category-card">
      <i class="fas fa-couch"></i><p>Perabot</p>
    </a>
    <a href="?" class="category-card">
      <i class="fas fa-th-large"></i><p>Semua</p>
    </a>
  </div>
</section>

<section class="featured">
  <h2> <?php echo !empty($category_filter) ? "Kategori " . htmlspecialchars($category_filter) : "Rekomendasi"; ?></h2>
  <div class="product-grid">
    <?php
    // Loop melalui data produk
    if ($product_result->num_rows > 0) {
        while ($row = $product_result->fetch_assoc()) {
            $product_id = $row['product_id'];
            echo "<div class='product-card'>";
            echo "<img src='../assets/uploads/" . htmlspecialchars(basename($row['image'])) . "' alt='" . htmlspecialchars($row['name']) . "' />";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>Rp " . number_format($row['price'], 0, ',', '.') . "</p>";
            echo "<a href='checkout-page.php?id=$product_id' class='buy-button'>Beli</a>";
            echo "</div>";
        }
    } else {
        echo "<p>Tidak ada produk yang tersedia untuk kategori ini.</p>";
    }
    ?>
  </div>
</section>
<footer>
        <p>&copy; <?php echo date("Y"); ?> ShopNest | Semua Hak Cipta Dilindungi</p>
    </footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const userMenu = document.querySelector(".user-menu");
    const dropdownMenu = document.querySelector(".dropdown-menu");

    userMenu.addEventListener("mouseenter", () => {
        dropdownMenu.style.display = "block";
    });

    userMenu.addEventListener("mouseleave", () => {
        dropdownMenu.style.display = "none";
    });
});
</script>

</body>
</html>
