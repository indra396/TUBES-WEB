<?php
session_start();
include('../includes/db.php'); // Koneksi database

// Mengecek apakah ada parameter order_id
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];

// Mendapatkan detail pesanan
$query = "SELECT * FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
</head>
<body>
    <h1>Pesanan Anda Telah Dikonfirmasi!</h1>
    <p>Nomor Pesanan: <?php echo $order['order_id']; ?></p>
    <p>Total Harga: Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></p>
    <p>Alamat Pengiriman: <?php echo htmlspecialchars($order['address']); ?></p>
    <p>Status Pesanan: <?php echo htmlspecialchars($order['status']); ?></p>

    <p>Terima kasih telah berbelanja di MyMarket!</p>
</body>
</html>
