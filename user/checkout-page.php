<?php
session_start();
include('../includes/db.php'); // Koneksi database
include('../includes/header.php'); 

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

// Mengecek apakah ada produk yang dipilih
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Mendapatkan data produk yang dipilih
    $query = "SELECT product_id, name, image, price FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
} else {
    echo "Produk tidak ditemukan!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - MyMarket</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="../assets/css/checkout.css">
</head>
<body>

<div class="checkout">
  <h1>Checkout</h1>
  
  <!-- Tabel Produk -->
  <table>
    <tr>
      <th>Gambar</th>
      <th>Nama Produk</th>
      <th>Harga</th>
      <th>Jumlah</th>
      <th>Total Harga</th>
    </tr>
    <tr>
      <td><img src="../assets/uploads/<?php echo htmlspecialchars(basename($product['image'])); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100"></td>
      <td><?php echo htmlspecialchars($product['name']); ?></td>
      <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
      <td>
        <input type="number" id="quantity" value="1" min="1" onchange="updateTotal()">
      </td>
      <td id="total-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
    </tr>
  </table>
  
  <!-- Form Pengiriman -->
  <h2>Form Pengiriman</h2>
  <form action="proses-checkout.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
    <input type="hidden" name="total_price" id="total-price-input" value="<?php echo $product['price']; ?>"> <!-- Hidden total price field -->
    
    <label for="full_name">Nama Pembeli</label>
    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
    
    <label for="phone">Nomor Telepon</label>
    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
    
    <label for="address">Alamat Lengkap</label>
    <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
    
    <label for="address_type">Kategori Alamat</label>
    <select name="address_type" id="address_type" required>
      <option value="home">Rumah</option>
      <option value="office">Kantor</option>
    </select>
    
    <label for="payment_method">Metode Pembayaran</label>
    <select name="payment_method" id="payment_method" required>
      <option value="cod">COD</option>
      <option value="bank_transfer">Transfer Bank</option>
    </select>
    
    <button type="submit">Proses Pembayaran</button>
  </form>
</div>

<script>
  function updateTotal() {
    var quantity = document.getElementById("quantity").value;
    var price = <?php echo $product['price']; ?>;
    var total = quantity * price;
    document.getElementById("total-price").textContent = "Rp " + total.toLocaleString('id-ID');
    document.getElementById("total-price-input").value = total; // Update the hidden total price field
  }
</script>

</body>
</html>