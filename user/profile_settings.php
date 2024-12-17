<?php
session_start();
include('../includes/db.php'); // Koneksi database
include('../includes/header.php');
include('../includes/footer.php');
// Mengecek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mendapatkan data pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Memproses form pembaruan data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Periksa apakah email sudah digunakan oleh pengguna lain
    $email_check_query = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
    $email_check_stmt = $conn->prepare($email_check_query);
    $email_check_stmt->bind_param("si", $email, $user_id);
    $email_check_stmt->execute();
    $email_check_result = $email_check_stmt->get_result();

    if ($email_check_result->num_rows > 0) {
        // Email sudah digunakan
        $error_message = "Email sudah digunakan oleh pengguna lain.";
    } else {
        // Lanjutkan pembaruan data
        $query = "UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success_message = "Profil berhasil diperbarui.";
        } else {
            $error_message = "Tidak ada perubahan yang disimpan.";
        }
    }
}

// Mengambil data pengguna untuk ditampilkan
$query = "SELECT full_name, email, phone, address FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil</title>
    <style>
        /* body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        } */

        h1 {
            /* margin-top: 25px; */
            text-align: center;
            color: #2c3e50;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .form-container input,
        .form-container textarea,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form textarea {
    resize: none;
    height: 100px;
}
        .form-container button {
            background: #2c3e50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .form-container button:hover {
            background: #34495e;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }

        .success {
            color: #27ae60;
        }

        .error {
            color: #e74c3c;
        }
    </style>
</head>
<body>

<h1>Pengaturan Profil</h1>

<div class="form-container">
    <?php if (isset($success_message)) : ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)) : ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="full_name">Nama Lengkap:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="phone">Nomor Telepon:</label>
        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

        <label for="address">Alamat:</label>
        <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>

        <button type="submit">Perbarui Profil</button>
    </form>
</div>

</body>
</html>
