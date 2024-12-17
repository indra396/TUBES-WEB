<?php
include('../includes/db.php');

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']); // Menghindari SQL Injection

    // Ambil nama file gambar dari database
    $result = $conn->query("SELECT image FROM products WHERE product_id = $product_id");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = __DIR__ . "/../assets/uploads/" . $row['image']; // Path absolut

        // Hapus gambar dari folder uploads
        if (file_exists($image_path)) {
            if (unlink($image_path)) {
                echo "Gambar berhasil dihapus.";
            } else {
                echo "Gagal menghapus gambar.";
            }
        } else {
            echo "File tidak ditemukan: $image_path";
        }
    }

    // Hapus data produk dari database
    $conn->query("DELETE FROM products WHERE product_id = $product_id");
}

// Redirect kembali ke halaman manage-products.php
header("Location: manage-products.php");
exit;
?>
