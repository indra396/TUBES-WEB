<?php
session_start();
include('../includes/db.php');

// Check if the user is an admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if the order_id is set in the URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Sanitize the order_id to prevent SQL injection
    $order_id = (int) $order_id; // Casting to integer for security

    // Query to delete the order from the database
    $query = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);

    // Execute the delete query
    if ($stmt->execute()) {
        // Redirect to the manage orders page after successful deletion
        header("Location: manage-orders.php");
        exit();
    } else {
        echo "Error: Could not delete the order.";
    }
} else {
    echo "Order ID not provided.";
}
?>
