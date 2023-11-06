<?php
require '../../include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dosen = $_POST['nama_dosen'];
    $nid = $_POST['nid'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert data ke tabel User
    $insertUserQuery = "INSERT INTO user (username, password, level) VALUES ('$username', '$hashedPassword', 'dosen')";
    if ($conn->query($insertUserQuery) === TRUE) {
        $userId = $conn->insert_id;

        // Insert data ke tabel Dosen
        $insertDosenQuery = "INSERT INTO dosen (id_user, nid, nama_dosen, email) VALUES ($userId, '$nid', '$nama_dosen', '$email')";
        if ($conn->query($insertDosenQuery) === TRUE) {
            header("Location: ../dosen.php?success=tambah");
            exit();
        } else {
            header("Location: ../dosen.php?error=tambah");
            exit();
        }
    } else {
        header("Location: ../dosen.php?error=tambah");
        exit();
    }
}

$conn->close();
