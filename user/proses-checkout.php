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

// Mendapatkan data dari form checkout
$product_id = $_POST['product_id'];
$total_price = $_POST['total_price'];
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$address_type = $_POST['address_type'];
$payment_method = $_POST['payment_method'];

// Validasi data
if (!preg_match('/^[0-9]+$/', $phone)) {
    echo "Nomor telepon tidak valid.";
    exit();
}

// Menyimpan data pengiriman ke database
$query = "INSERT INTO orders (user_id, product_id, full_name, phone, address, address_type, payment_method, total_price, status) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'dikemas')";
$stmt = $conn->prepare($query);
$stmt->bind_param("iisssssd", $user_id, $product_id, $full_name, $phone, $address, $address_type, $payment_method, $total_price);

if ($stmt->execute()) {
    // Mengambil ID order yang baru saja dimasukkan
    $order_id = $stmt->insert_id;

    if ($payment_method === 'bank_transfer') {
        // Redirect ke halaman konfirmasi pembayaran
        header("Location: payment-confirmation.php?order_id=$order_id");
    } else if ($payment_method === 'cod') {
        // Update status pesanan ke 'dikemas' dan redirect ke halaman dikemas
        // $update_query = "UPDATE orders SET status = 'dikemas' WHERE order_id = ?";
        // $update_stmt = $conn->prepare($update_query);
        // $update_stmt->bind_param("i", $order_id);
        // $update_stmt->execute();

        header("Location: dikemas.php?order_id=$order_id");
    }
    exit();
} else {
    // Logging error untuk debugging
    error_log("Error executing query: " . $stmt->error);
    echo "Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.";
    exit();
}
?>
