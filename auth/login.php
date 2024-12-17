<?php
session_start();
include('../includes/db.php'); // Koneksi database

$error = ''; // Inisialisasi variabel error

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Mempersiapkan dan mengeksekusi query
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Memeriksa kredensial pengguna
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            header("Location: " . ($user['role'] == 'admin' ? '../admin/dashboard.php' : '../user/dashboard.php'));
            exit(); // Pastikan untuk menghentikan eksekusi setelah redirect
        } else {
            $error = "Email atau password salah!";
        }
    } else {
        $error = "Terjadi kesalahan saat mempersiapkan query.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>
    <form method="post">
        <h2>Login</h2>
        <input type="email" name="email" placeholder="Email" required>
        <div class="password-container">
            <input type="text" id="password" name="password" placeholder="Password" required>
            <button type="button" id="togglePassword">
                <!-- Ganti dengan gambar mata lokal -->
                <img id="eyeIcon" src="../assets/uploads/view.png" alt="Eye Icon" width="24" height="24">
            </button>
        </div>

        <button type="submit" name="login">Login</button>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <div class="additional-options">
            <a href="forgot_password.php" class="forgot-password">Lupa Password?</a>
            <p>Belum punya akun? <a href="signup.php" class="signup-link">Daftar Sekarang</a></p>
        </div>
    </form>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            // Ganti ikon mata
            if (isPassword) {
                eyeIcon.src = '../assets/uploads/view.png'; // Ganti dengan gambar mata terbuka
            } else {
                eyeIcon.src = '../assets/uploads/hide.png'; // Ganti dengan gambar mata tertutup
            }
        });
    </script>
</body>
</html>
