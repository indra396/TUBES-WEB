<?php
include('../includes/db.php');

if (isset($_POST['signup'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); //mengubah password
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $query = "INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $full_name, $email, $password, $phone, $address);
    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        $error = "Gagal mendaftar, coba lagi!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/signup.css">
</head>
<body>
    <form method="post">
        <h2>Registrasi</h2>
            <input type="text" name="full_name" placeholder="Nama Lengkap" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="number" name="phone" placeholder="Nomor Telepon" required>
            <textarea name="address" placeholder="Alamat" required></textarea>
            <button type="submit" name="signup">Daftar</button>
            <div class="additional-options">
                <p>Sudah punya akun? <a href="login.php">Login sekarang</a></p>
            </div>
    </form>
</body>
</html>


