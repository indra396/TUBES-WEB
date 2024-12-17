<?php
session_start();
include('../includes/db.php'); // Koneksi database
include('../includes/header.php');
include('../includes/footer.php');

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mengecek apakah order_id ada di URL
if (!isset($_GET['order_id'])) {
    echo "ID Pesanan tidak ditemukan!";
    exit();
}

$order_id = $_GET['order_id'];

// Mendapatkan informasi pesanan berdasarkan order_id
$query = "SELECT o.order_id, o.total_price, o.status, p.name AS product_name, p.image 
          FROM orders o
          JOIN products p ON o.product_id = p.product_id
          WHERE o.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Jika pesanan tidak ditemukan
if (!$order) {
    echo "Pesanan tidak ditemukan!";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan - Sedang Dikemas</title>
    <link rel="stylesheet" href="../assets/css/dikemas.css">
</head>
<body>

<div class="dikemas">
    <h1>Pesanan Sedang Dikemas</h1>

    <div class="order-details">
        <h2>Detail Pesanan</h2>
        <img src="../assets/uploads/<?php echo htmlspecialchars(basename($order['image'])); ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>" width="100">
        <p><strong>Produk:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
        <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></p>
    </div>

    <div class="status-message">
        <p>Pesanan Anda sedang diproses dan dikemas. Harap tunggu kurir kami untuk mengantarkan pesanan ke alamat Anda.</p>
        <p>Terima kasih telah memilih metode pembayaran COD!</p>
    </div>

    <a href="dashboard.php" class="back-home">Kembali ke Beranda</a>
</div>

</body>
</html>
