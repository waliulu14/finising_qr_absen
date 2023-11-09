<?php
// Sambungkan ke database
require_once '../include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tanda_tangan'])) {
    $tandaTangan = $_POST['tanda_tangan'];

    // Simpan tanda tangan ke database (gantilah dengan koneksi dan tabel yang sesuai)
    $queryInsertTandaTangan = "INSERT INTO tanda_tangan_dosen (id_dosen, tgl_tanda_tangan, tanda_tangan) 
                              VALUES (1, NOW(), ?)";

    $stmt = $conn->prepare($queryInsertTandaTangan);
    $stmt->bind_param("s", $tandaTangan);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
