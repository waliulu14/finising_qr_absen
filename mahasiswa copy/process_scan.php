<?php
include('../include/config.php');

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $kode_qr_mahasiswa = $data->kode_qr_mahasiswa;

    try {
        // Check if the QR code has been scanned before
        $checkQuery = $db->prepare("SELECT status_absen FROM absensi WHERE kode_qr = :kode_qr_mahasiswa");
        $checkQuery->bindParam(':kode_qr_mahasiswa', $kode_qr_mahasiswa);
        $checkQuery->execute();
        $result = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['status_absen'] == 0) {
            // If the QR code hasn't been scanned, update the status
            $updateQuery = $db->prepare("UPDATE absensi SET status_absen = 1 WHERE kode_qr = :kode_qr_mahasiswa");
            $updateQuery->bindParam(':kode_qr_mahasiswa', $kode_qr_mahasiswa);
            $updateQuery->execute();

            $pesan = "Absensi berhasil dicatat.";
        } else {
            $pesan = "QR code sudah di-scan sebelumnya atau tidak valid.";
        }
    } catch (PDOException $e) {
        die("Error updating data in the database: " . $e->getMessage());
    }
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode(['message' => $pesan]);
?>
