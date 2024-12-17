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
$query = "SELECT o.order_id, o.total_price, o.payment_method, o.payment_proof, p.name AS product_name 
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
    <title>Pesanan Berhasil</title>
    <link rel="stylesheet" href="../assets/css/payment_success.css">
</head>
<body>

<div class="payment-success">
    <h1>Pesanan Berhasil!</h1>

    <div class="order-details">
        <h2>Detail Pesanan</h2>
        <p><strong>ID Pesanan:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
        <p><strong>Produk:</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
        <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></p>
        <p><strong>Metode Pembayaran:</strong> <?php echo $order['payment_method'] === 'bank_transfer' ? 'Transfer Bank' : 'COD'; ?></p>
    </div>

    <?php if ($order['payment_method'] === 'bank_transfer'): ?>
        <div class="payment-proof">
            <h2>Bukti Pembayaran</h2>
            <?php if ($order['payment_proof']): ?>
                <p>Bukti pembayaran berhasil diunggah:</p>
                <img src="../assets/uploads/proofs/<?php echo htmlspecialchars($order['payment_proof']); ?>" alt="Bukti Pembayaran" width="300">
            <?php else: ?>
                <p>Bukti pembayaran belum diunggah. Silakan hubungi kami jika ada masalah.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="success-message">
        <p>Terima kasih telah berbelanja di MyMarket! Kami akan segera memproses pesanan Anda.</p>
    </div>

    <a href="dashboard.php" class="back-home">Kembali ke Beranda</a>
</div>

</body>
</html>
