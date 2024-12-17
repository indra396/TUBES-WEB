<?php
include('../includes/db.php');

$product_id = $_GET['product_id'];
$data = $conn->query("SELECT * FROM products WHERE product_id = $product_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $image = $_FILES['image'];

    $image_path = $data['image']; // Default ke gambar lama

// Jika ada gambar baru yang diunggah
if ($image && isset($image['tmp_name']) && $image['tmp_name']) {         
    $target_dir = __DIR__ . "/../assets/uploads/";         
    $unique_name = time() . "_" . basename($image['name']); // Nama unik
    $target_file = $target_dir . $unique_name;         
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));      

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];         
    if (in_array($imageFileType, $allowed_types)) {             
        if (is_dir($target_dir) && is_writable($target_dir)) {                 
            if (move_uploaded_file($image['tmp_name'], $target_file)) {  
                $old_image_path = __DIR__ . "/../" . $data['image']; // Path gambar lama
                if (!empty($data['image']) && file_exists($old_image_path)) {
                    if (!unlink($old_image_path)) {
                        echo "Gagal menghapus gambar lama: " . $old_image_path;
                    }
                }

                $image_path = "assets/uploads/" . $unique_name; // Path baru
            } else {
                echo "Gagal mengunggah file.";
            }
        } else {
            echo "Direktori penyimpanan tidak dapat diakses.";
        }
    } else {
        echo "Format file tidak didukung.";
    }
}


    // Update produk di database
    $conn->query("UPDATE products SET 
        name = '$name', 
        description = '$description', 
        price = '$price', 
        stock = '$stock', 
        category = '$category',
        image = '$image_path'
        WHERE product_id = $product_id");

    header("Location: manage-products.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../assets/css/add_product.css">
</head>
<script>

document.getElementById('image-input').addEventListener('change', function(event) {
    const file = event.target.files[0]; // Ambil file dari input
    if (file) {
        const reader = new FileReader(); // Membuat FileReader untuk membaca file
        reader.onload = function(e) {
            const imgPreview = document.getElementById('product-image-preview');
            imgPreview.src = e.target.result; // Atur sumber gambar menjadi hasil pembacaan
            console.log('Gambar berhasil dimuat'); // Log ke konsol untuk debugging
        };
        reader.readAsDataURL(file); // Membaca file sebagai Data URL
    }
});

</script>

<body>
    <h1>Edit Produk</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Nama Produk:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required><br>
        <label>Deskripsi:</label>
        <input type="text" name="description" value="<?= htmlspecialchars($data['description']) ?>" required><br>
        <label>Harga Produk:</label>
        <input type="number" name="price" value="<?= htmlspecialchars($data['price']) ?>" required><br>
        <label>Stok Produk:</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($data['stock']) ?>" required><br>
        <label>Kategori Produk:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($data['category']) ?>" required><br>
        <label>Gambar:</label><br>
        <img src="../assets/uploads/<?= htmlspecialchars(basename($data['image'])); ?>?<?= time(); ?>" 
     id="product-image-preview" style="max-width: 100px; height: auto;"><br>

        <input type="file" name="image" id="image-input"accept="image/*" ><br>
        <br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
