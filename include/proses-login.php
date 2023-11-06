<?php
session_start();

// Sisipkan berkas konfigurasi database
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pass']; // Sesuaikan dengan nama input password

    // Query untuk mendapatkan hash password pengguna berdasarkan username
    $query = "SELECT id, level, username, password FROM user WHERE username = '$username'"; // Tambahkan 'username' ke query
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Memeriksa apakah password yang di-inputkan cocok dengan hash
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_level'] = $row['level'];
            $_SESSION['username'] = $row['username']; // Tambahkan 'username' ke sesi

            // Redirect sesuai dengan level pengguna
            if ($_SESSION['user_level'] == 'admin') {
                header('Location: ../admin/dashboard.php');
            } elseif ($_SESSION['user_level'] == 'dosen') {
                header('Location: ../dosen/index.php');
            } elseif ($_SESSION['user_level'] == 'mahasiswa') {
                header('Location: ../mahasiswa/index.php');
            }
        } else {
            // Password salah, berikan pesan kesalahan
            $error = "Username atau password salah";
            header('Location: ../index.php?error=' . $error);
        }
    } else {
        // Username tidak ditemukan, berikan pesan kesalahan
        $error = "Username tidak ditemukan";
        header('Location: ../index.php?error=' . $error);
    }
}

// Tutup koneksi database
$conn->close();
?>
