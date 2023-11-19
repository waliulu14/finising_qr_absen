<?php
session_start();
require '../../include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $level = 'mahasiswa';
    $nim = $_POST['nim'];
    $nama_mahasiswa = $_POST['nama_mahasiswa'];
    $milis = $_POST['milis'];
    $email = $_POST['email'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert data ke tabel User
    $insertUserQuery = "INSERT INTO user (username, password, level) VALUES ('$username', '$hashedPassword', '$level')";
    if ($conn->query($insertUserQuery) === TRUE) {
        $userId = $conn->insert_id;

        // Insert data ke tabel Mahasiswa
        $insertMahasiswaQuery = "INSERT INTO mahasiswa (id_user, nama_mahasiswa, nim, milis, email) VALUES ($userId, '$nama_mahasiswa', '$nim', '$milis', '$email')";
        if ($conn->query($insertMahasiswaQuery) === TRUE) {
            header("Location: ../mahasiswa.php?success=tambah");
            exit();
        } else {
            header("Location: ../mahasiswa.php?error=tambah");
            exit();
        }
    } else {
        header("Location: ../mahasiswa.php?error=tambah");
        exit();
    }
}
?>
