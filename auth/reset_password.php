<?php
session_start();
include('../includes/db.php');

// Cek apakah token ada di URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cek apakah token valid dan belum expired
    $query = "SELECT user_id, reset_token_expiry FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $expiry_time = $user['reset_token_expiry'];

        // Cek apakah token belum expired
        if (strtotime($expiry_time) > time()) {
            if (isset($_POST['reset_password'])) {
                $new_password = $_POST['new_password'];
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password dan hapus token reset
                $update_query = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
                $stmt_update = $conn->prepare($update_query);
                $stmt_update->bind_param("ss", $hashed_password, $token);
                $stmt_update->execute();

                echo "<script>alert('Password berhasil diubah.'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Token telah expired.');</script>";
        }
    } else {
        echo "<script>alert('Token tidak valid.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        input[type="password"] {
            width: 100%;
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
    <h2>Reset Password</h2>
    <form method="POST">
        <label for="new_password">Password Baru:</label>
        <input type="password" name="new_password" id="new_password" required>
        <button type="submit" name="reset_password">Reset Password</button>
    </form>
</div>

</body>
</html>
