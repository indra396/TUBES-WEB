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

// Mendapatkan data pengguna
$user_id = $_SESSION['user_id'];
$query = "SELECT full_name, email, phone, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Mendapatkan data riwayat pesanan pengguna
$user_id = $_SESSION['user_id'];

// Memeriksa status yang dipilih
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'dikemas';
$query = "SELECT o.order_id, p.name AS product_name, p.image, o.total_price, o.status, o.order_date 
          FROM orders o 
          JOIN products p ON o.product_id = p.product_id 
          WHERE o.user_id = ? AND o.status = ?
          ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $status_filter);
$stmt->execute();
$result = $stmt->get_result();

// Konfirmasi pesanan diterima
if (isset($_POST['confirm_order'])) {
    $order_id = $_POST['order_id'];

    // Update status menjadi "diterima"
    $update_query = "UPDATE orders SET status = 'diterima' WHERE order_id = ? AND user_id = ? AND status = 'dikirim'";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("ii", $order_id, $user_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Pesanan telah dikonfirmasi!'); window.location.href='riwayat.php?status=diterima';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan, silakan coba lagi.');</script>";
    }
}

// Batalkan pesanan
if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];

    // Update status menjadi "dibatalkan"
    $cancel_query = "UPDATE orders SET status = 'dibatalkan' WHERE order_id = ? AND user_id = ? AND status = 'dikemas'";
    $stmt_cancel = $conn->prepare($cancel_query);
    $stmt_cancel->bind_param("ii", $order_id, $user_id);

    if ($stmt_cancel->execute()) {
        echo "<script>alert('Pesanan berhasil dibatalkan.'); window.location.href='riwayat.php?status=dikemas';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat membatalkan pesanan.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="../uploads/css/riwayat.css">
    <style>
       
        .title-container { text-align: center; margin: 20px 0; }
        .title-container h2 { margin: 0; font-size: 1.8rem; color: #2c3e50; }
        nav { display: flex; justify-content: center; background: #34495e; padding: 10px 0; }
        nav a { color: #fff; text-decoration: none; margin: 0 15px; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        .tabs { display: flex; justify-content: center; margin: 20px 0; }
        .tabs a { padding: 10px 20px; margin: 0 5px; text-decoration: none; color: #2c3e50; border: 1px solid #ccc; border-radius: 5px; }
        .tabs a.active { background: #2c3e50; color: #fff; }
        .order-history { display: flex; flex-direction: column; align-items: center; margin: 20px; }
        .order-card { display: flex; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 20px; width: 80%; padding: 10px; }
        .order-card img { width: 100px; height: 100px; border-radius: 8px; }
        .order-details { margin-left: 20px; }
        .order-details h3 { margin: 0; }
        .status { font-weight: bold; text-transform: capitalize; }
        .status.dikemas { color: #f39c12; }
        .status.dikirim { color: #3498db; }
        .status.dibatalkan { color: #e74c3c; }
        .status.diterima { color: #2ecc71; }
        .no-data { text-align: center; color: #888; margin-top: 20px; }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; color: #fff; }
        .btn-cancel { background-color: #e74c3c; }
        .btn-confirm { background-color: #2ecc71; }
   
   </style>
</head>
<body>


<!-- Title Section -->
<div class="title-container">
    <h2>Riwayat Pesanan</h2>
</div>

<!-- Tabs for Filtering -->
<div class="tabs">
    <a href="?status=dikemas" class="<?php echo $status_filter == 'dikemas' ? 'active' : ''; ?>">Dikemas</a>
    <a href="?status=dikirim" class="<?php echo $status_filter == 'dikirim' ? 'active' : ''; ?>">Dikirim</a>
    <a href="?status=dibatalkan" class="<?php echo $status_filter == 'dibatalkan' ? 'active' : ''; ?>">Dibatalkan</a>
    <a href="?status=diterima" class="<?php echo $status_filter == 'diterima' ? 'active' : ''; ?>">Diterima</a>
</div>

<!-- Main Content -->
<main>
    <div class="order-history">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='order-card'>";
                echo "<img src='../assets/uploads/" . htmlspecialchars(basename($row['image'])) . "' alt='" . htmlspecialchars($row['product_name']) . "' />";
                echo "<div class='order-details'>";
                echo "<h3>" . htmlspecialchars($row['product_name']) . "</h3>";
                echo "<p>Total: Rp " . number_format($row['total_price'], 0, ',', '.') . "</p>";
                echo "<p>Status: <span class='status " . htmlspecialchars($row['status']) . "'>" . ucfirst($row['status']) . "</span></p>";
                echo "<p>Tanggal: " . date("d M Y, H:i", strtotime($row['order_date'])) . "</p>";

                // Tombol konfirmasi atau batalkan
                if ($row['status'] == 'dikemas') {
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>";
                    echo "<button type='submit' name='cancel_order' class='btn btn-cancel'>Batalkan Pesanan</button>";
                    echo "</form>";
                } 
                elseif ($row['status'] == 'dikirim') {
                    echo "<form method='POST' action=''>";
                    echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($row['order_id']) . "'>";
                    echo "<button type='submit' name='confirm_order' class='btn btn-confirm'>Pesanan Diterima</button>";
                    echo "</form>";
                }
                echo "</div></div>";
            }
        } else {
            echo "<p class='no-data'>Tidak ada pesanan dengan status <strong>" . ucfirst($status_filter) . "</strong>.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
