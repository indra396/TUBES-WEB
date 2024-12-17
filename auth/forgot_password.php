<?php
session_start();
include('../includes/db.php');

if (isset($_POST['submit_email'])) {
    $email = $_POST['email'];

    // Cek apakah email terdaftar di database
    $query = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate token
        $token = bin2hex(random_bytes(50));
        $expiry_time = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Simpan token dan expiry_time di database
        $update_query = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("sss", $token, $expiry_time, $email);
        $stmt_update->execute();

        // Kirim email dengan link reset password
        $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token;
        $subject = "Reset Password Request";
        $message = "Klik link berikut untuk mereset password Anda: " . $reset_link;
        $headers = "From: no-reply@yourwebsite.com";

        if (mail($email, $subject, $message, $headers)) {
            echo "<script>alert('Link reset password telah dikirim ke email Anda.');</script>";
        } else {
            echo "<script>alert('Gagal mengirim email.');</script>";
        }
    } else {
        echo "<script>alert('Email tidak terdaftar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <style>
        /* Apply a full-screen layout */
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        /* Flexbox to center the container vertically and horizontally */
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Center the container */
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 14px;
            color: #555;
        }

        input[type="email"] {
            width: 92%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 15px;
        }

        .message a {
            color: #007bff;
            text-decoration: none;
        }

        .message a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Lupa Password</h2>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <button type="submit" name="submit_email">Kirim Link Reset</button>
    </form>
    
    <div class="message">
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</div>

</body>
</html>
