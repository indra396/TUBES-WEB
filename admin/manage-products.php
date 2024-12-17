<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Query untuk mendapatkan data
$result = $conn->query("SELECT * FROM products");

?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk</title>
    <link rel="stylesheet" href="../assets/css/manage-products.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage-products.php">Produk</a></li>
                    <li><a href="manage-orders.php">Order</a></li>
                    <li><a href="manage-users.php">User</a></li>
                    <li><a href="../index.php" class="logout">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Daftar Produk</h1>
                <a href="add_product.php" class="add-button">Tambah Produk</a>
            </header>

            <section class="product-table">
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_id']); ?></td>
                                <td><?= htmlspecialchars($row['name']); ?></td>
                                <td><?= htmlspecialchars($row['description']); ?></td>
                                <td>Rp <?= number_format($row['price'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($row['stock']); ?></td>
                                <td>
                                    <img src="../assets/uploads/<?= htmlspecialchars(basename($row['image'])); ?>" 
                                         alt="<?= htmlspecialchars($row['name']); ?>" 
                                         style="max-width: 100px; height: auto;">
                                </td>
                                <td>
                                    <a href="edit_product.php?product_id=<?= $row['product_id']; ?>" class="edit-button">Edit</a>
                                    <a href="delete_product.php?product_id=<?= $row['product_id']; ?>" class="delete-button" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
