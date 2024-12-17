<?php 
session_start(); 
include('../includes/db.php');  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {     
    $name = $_POST['name'] ?? null;     
    $description = $_POST['description'] ?? null;     
    $price = $_POST['price'] ?? 0;     
    $stock = $_POST['stock'] ?? 0;     
    $category = $_POST['category'] ?? null;     
    $image = $_FILES['image'] ?? null;      

    if ($image && isset($image['tmp_name']) && $image['tmp_name']) {         
        // Folder penyimpanan         
        $target_dir = __DIR__ . "/../assets/uploads/"; // Menggunakan __DIR__         
        $target_file = $target_dir . basename($image['name']);         
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));          

        // Validasi file gambar         
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];         
        if (in_array($imageFileType, $allowed_types)) {             
            if (is_dir($target_dir) && is_writable($target_dir)) {                 
                if (move_uploaded_file($image['tmp_name'], $target_file)) {                     
                    // Simpan data ke database                     
                    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category, image) VALUES (?, ?, ?, ?, ?, ?)");                     
                    if ($stmt) {                         
                        $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $category, $target_file);                         
                        if ($stmt->execute()) {                             
                            header("Location: manage-products.php");                             
                            exit();                         
                        } else {                             
                            echo "Error: " . $stmt->error;                         
                        }                         
                        $stmt->close();                     
                    } else {                         
                        echo "Error preparing statement: " . $conn->error;                     
                    }                 
                } else {                     
                    echo "Error uploading file.";                 
                }             
            } else {                 
                echo "Upload directory does not exist or is not writable.";             
            }         
        } else {             
            echo "Invalid file type. Allowed types: jpg, jpeg, png, gif.";         
        }     
    } else {         
        echo "No file uploaded or invalid file.";     
    } 
} 
?>    

<!DOCTYPE html> 
<html> 
<head>     
    <title>Tambah Produk</title>     
    <link rel="stylesheet" href="../assets/css/add_product.css"> 
</head> 
<body>    
     
    <h1>Tambah Produk</h1> 
    <form action="add_product.php" method="POST" enctype="multipart/form-data">     
        <label for="name">Product Name:</label>     
        <input type="text" id="name" name="name" required><br>      

        <label for="description">Description:</label>     
        <textarea id="description" name="description" required></textarea><br>      

        <label for="price">Price:</label>     
        <input type="number" id="price" name="price" step="0.01" required><br>      

        <label for="stock">Stock:</label>     
        <input type="number" id="stock" name="stock" required><br>      

        <label for="category">Category:</label>     
        <input type="text" id="category" name="category" required><br>      

        <label for="image">Product Image:</label>     
        <input type="file" id="image" name="image" accept="image/*" required><br>      

        <button type="submit">Add Product</button> 
    </form> 
</body> 
</html>
