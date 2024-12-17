<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Query to count the total products
$queryProducts = "SELECT COUNT(*) AS total_products FROM products";
$resultProducts = $conn->query($queryProducts);
$totalProducts = $resultProducts->fetch_assoc()['total_products'];

// Query to count the total orders
$queryOrders = "SELECT COUNT(*) AS total_orders FROM orders";
$resultOrders = $conn->query($queryOrders);
$totalOrders = $resultOrders->fetch_assoc()['total_orders'];

// Query to count the total users
$queryUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultUsers = $conn->query($queryUsers);
$totalUsers = $resultUsers->fetch_assoc()['total_users'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard_admin.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="#">Dashboard</a></li>
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
                <h1>Welcome, Admin</h1>
                <p>Here is your dashboard overview.</p>
            </header>

            <section class="cards">
                <div class="card">
                    <h3>Total Produk</h3>
                    <p><?php echo $totalProducts; ?></p>
                </div>
                <div class="card">
                    <h3>Total Order</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                <div class="card">
                    <h3>System Uptime</h3>
                    <p>99.99%</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
