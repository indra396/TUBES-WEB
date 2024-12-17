<?php
session_start();
include('../includes/db.php'); // Koneksi database

// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan data dari form
$order_id = $_POST['order_id'];

if (isset($_FILES['proof']) && $_FILES['proof']['error'] == UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['proof']['tmp_name'];
    $file_name = basename($_FILES['proof']['name']);
    $upload_dir = "../assets/uploads/proofs/";  // Fixed relative path

    // Membuat direktori jika belum ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Mengatur path file tujuan
    $file_path = $upload_dir . $file_name;

    // Memindahkan file ke folder tujuan
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Menyimpan path bukti pembayaran ke database
        $query = "UPDATE orders SET payment_proof = ? WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $file_name, $order_id);

        if ($stmt->execute()) {
            echo "Bukti pembayaran berhasil diunggah!";
            header("Location: payment-success.php?order_id=$order_id");
            exit();
        } else {
            echo "Terjadi kesalahan saat menyimpan data bukti pembayaran.";
        }
    } else {
        echo "Gagal mengunggah file.";
    }
} else {
    echo "File tidak valid.";
}
?>
