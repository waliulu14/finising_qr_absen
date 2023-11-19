<?php
require_once '../../include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dosenId = $_POST['dosen'];
    $kodeMatkul = $_POST['kode_matkul'];
    $namaMatkul = $_POST['nama_matkul'];
    $kelas = $_POST['kelas'];
    $hari = $_POST['hari'];
    $jamMulai = $_POST['jam_mulai'];
    $jamSelesai = $_POST['jam_selesai'];

    // Insert the new course into the Matkul table
    $insertQuery = "INSERT INTO matkul (id_dosen, kode_matkul, nama_matkul, kelas, hari, jam_mulai, jam_selesai)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("issssss", $dosenId, $kodeMatkul, $namaMatkul, $kelas, $hari, $jamMulai, $jamSelesai);

    if ($stmt->execute()) {
        // Redirect back to the Mata Kuliah page with a success message
        header("Location: ../matakuliah.php?success=tambah");
    } else {
        // Redirect back to the Mata Kuliah page with an error message
        header("Location: ../matakuliah.php?error=tambah");
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
} else {
    // If the request method is not POST, redirect to the Mata Kuliah page
    header("Location: matakuliah.php");
}
