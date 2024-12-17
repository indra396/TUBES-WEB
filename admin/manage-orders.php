<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Query untuk mendapatkan semua data pesanan
$query = "SELECT orders.order_id, orders.full_name, products.name AS product_name, orders.total_price, orders.status, orders.order_date
          FROM orders
          JOIN products ON orders.product_id = products.product_id";
$result = $conn->query($query);

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    // Mendapatkan data order berdasarkan order_id
    $query = "SELECT orders.order_id, orders.product_id, orders.full_name, orders.phone, orders.address, orders.address_type, 
                     orders.payment_method, products.price, orders.status, products.name AS product_name, products.image AS product_image 
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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/css/manage_orders.css">
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
                <h1>Manage Orders</h1>
                <p>Here you can view, update, or delete order data.</p>
            </header>

            <section class="order-management">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Produk</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Tanggal Pesanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    if ($result && $result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['full_name']}</td>
                    <td>{$row['product_name']}</td>
                    <td>Rp" . number_format($row['total_price'], 0, ',', '.') . "</td>
                    <td>{$row['status']}</td>
                    <td>{$row['order_date']}</td>
                    <td>
                        <a href='delete_order.php?order_id=" . $row['order_id'] . "' class='btn-delete' onclick='return confirm(\"Are you sure you want to delete this order?\")'>Delete</a>
                        <a href='order_detail.php?order_id=" . $row['order_id'] . "' class='btn-detail'>Detail</a>
                    </td>
                </tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='7'>No orders found.</td></tr>";
    }
    ?>
</tbody>

                </table>
            </section>
        </main>
    </div>
</body>
</html>
