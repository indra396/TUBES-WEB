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

// Mendapatkan ID order dari parameter URL
if (!isset($_GET['order_id'])) {
    echo "ID Pesanan tidak ditemukan!";
    exit();
}

$order_id = $_GET['order_id'];

// Mengecek apakah order_id ada di URL
if (!isset($_GET['order_id'])) {
    echo "ID Pesanan tidak ditemukan!";
    exit();
}

$order_id = $_GET['order_id'];

// Mendapatkan informasi pesanan berdasarkan order_id
$query = "SELECT o.order_id, o.total_price, o.status, p.name AS product_name 
          FROM orders o
          JOIN products p ON o.product_id = p.product_id
          WHERE o.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Informasi rekening penjual
$seller_account_name = "MyMarket Official";
$seller_account_number = "123456789";
$seller_bank_name = "Bank Mandiri";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran</title>
    <link rel="stylesheet" href="../assets/css/payment-confirmation.css">
</head>
<body>

<div class="payment-confirmation">
    <h1>Konfirmasi Pembayaran</h1>

    <div class="account-info">
        <h2>Informasi Rekening Penjual</h2>
        <p><strong>Nama Rekening:</strong> <?php echo $seller_account_name; ?></p>
        <p><strong>Nomor Rekening:</strong> <?php echo $seller_account_number; ?></p>
        <p><strong>Bank:</strong> <?php echo $seller_bank_name; ?></p>
        <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></p>
    </div>

    <h2>Unggah Bukti Pembayaran</h2>
    <form action="upload-proof.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

        <label for="proof">Foto Bukti Pembayaran:</label>
        <input type="file" name="proof" id="proof" accept="image/*" required>

        <button type="submit">Unggah Bukti</button>
    </form>
</div>

</body>
</html>
