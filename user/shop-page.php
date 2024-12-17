<?php
session_start();
include('../includes/db.php'); // Koneksi database

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mengecek apakah ID produk tersedia
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$_GET['id'];

// Mengambil data pengguna
$query = "SELECT full_name, email, phone, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    header("Location: login.php");
    exit();
}

// Mengambil data produk berdasarkan ID produk
$query = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

// Menangani aksi form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = 1;  // Set the quantity to 1 by default
    $action = $_POST['action'];

    // Aksi Beli Sekarang
    if ($action === 'buy_now') {
        $_SESSION['cart'] = [[
            'product_id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image']
        ]];
        header("Location: checkout-page.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk</title>
  <link rel="stylesheet" href="../assets/css/shop_page.css">
</head>
<body>

<header>
  <div class="logo">MyMarket</div>
  <div class="user-menu">
    <a class="cart" href="cart-page.php"><i class="fas fa-shopping-cart"></i></a>
    <i class="fas fa-user-circle"></i>
    <p><?php echo htmlspecialchars($user['full_name']); ?></p>
  </div>
</header>

<div class="product-detail">
  <div class="product-info">
    <img src="../assets/uploads/<?php echo htmlspecialchars(basename($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">

    <div class="product-details">
      <h1><?php echo htmlspecialchars($product['name']); ?></h1>
      <p>Harga: Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
      <p>Stok Tersedia: <?php echo $product['stock']; ?></p>
      <p><?php echo htmlspecialchars($product['description']); ?></p>
      <form action="shop-page.php?id=<?php echo $product_id; ?>" method="POST">
        <button type="submit" name="action" value="buy_now">Beli Sekarang</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
