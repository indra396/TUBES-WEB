<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Query untuk mendapatkan semua data pesanan
$query = "SELECT orders.order_id, orders.full_name, products.name AS product_name, orders.total_price, orders.status, orders.order_date, orders.payment_proof
          FROM orders
          JOIN products ON orders.product_id = products.product_id";
$result = $conn->query($query);

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    // Mendapatkan data order berdasarkan order_id
    $query = "SELECT orders.order_id, orders.product_id, orders.full_name, orders.phone, orders.address, orders.address_type, 
    orders.payment_method, orders.total_price, orders.status, orders.order_date, orders.payment_proof,
    products.name AS product_name, products.image AS product_image 
    FROM orders
    JOIN products ON orders.product_id = products.product_id
    WHERE orders.order_id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        echo "Pesanan tidak ditemukan.";
        exit();
    }

    // Proses konfirmasi atau pembatalan pesanan
    if (isset($_POST['confirm_order'])) {
        $update_query = "UPDATE orders SET status = 'dikirim' WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            header("Location: order_detail.php?order_id=$order_id");
            exit();
        } else {
            echo "Terjadi kesalahan saat mengonfirmasi pesanan.";
        }
    }

    if (isset($_POST['cancel_order'])) {
        $update_query = "UPDATE orders SET status = 'dibatalkan' WHERE order_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            header("Location: order_detail.php?order_id=$order_id");
            exit();
        } else {
            echo "Terjadi kesalahan saat membatalkan pesanan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
    <link rel="stylesheet" href="../assets/css/order_detail.css">
</head>
<body>
    
    <div class="detail-container">
        <h1>Detail Pesanan</h1>
        <table>
            <tr>
                <th>Nama Pelanggan</th>
                <td><?= $order['full_name']; ?></td>
            </tr>
            <tr>
                <th>Nama Produk</th>
                <td><?= $order['product_name']; ?></td>
            </tr>
            <tr>
                <th>Nomor Telepon</th>
                <td><?= $order['phone']; ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?= $order['address']; ?></td>
            </tr>
            <tr>
                <th>Total Harga</th>
                <td>Rp<?= number_format($order['total_price'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= $order['status']; ?></td>
            </tr>
            <tr>
                <th>Tanggal Pesanan</th>
                <td><?= $order['order_date']; ?></td>
            </tr>
            <tr>
                <th>Bukti Pembayaran</th>
                <td>
                    <?php if ($order['payment_proof']): ?>
                        <img src="../assets/uploads/proofs/<?= htmlspecialchars(basename($order['payment_proof'])); ?>" alt="Bukti Pembayaran" width="200">
                    <?php else: ?>
                        <p>COD</p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <!-- Tombol untuk konfirmasi dan pembatalan pesanan -->
        <form method="POST">
            <?php if ($order['status'] !== 'dikirim' && $order['status'] !== 'dibatalkan' && $order['status'] !== 'diterima'): ?><br>
                <button type="submit" name="confirm_order" class="btn-confirm">Konfirmasi Pesanan</button>
                <button type="submit" name="cancel_order" class="btn-cancel">Batalkan Pesanan</button>
            <?php elseif ($order['status'] === 'dikirim'): ?>
                <p>Pesanan telah dikonfirmasi.</p>
            <?php elseif ($order['status'] === 'dibatalkan'): ?>
                <p>Pesanan telah dibatalkan.</p>
            <?php elseif ($order['status'] === 'diterima'): ?>
                <p>Pesanan telah diterima dan tidak dapat dibatalkan atau dikonfirmasi lagi.</p>
            <?php endif; ?>
        </form>

        <a href="manage-orders.php" class="btn-back">Kembali</a>
    </div>
</body>
</html>

