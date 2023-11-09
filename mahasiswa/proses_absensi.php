<?php
require_once '../include/config.php'; // Sesuaikan dengan path ke berkas config Anda

// Terima data absensi dari mahasiswa
$data = json_decode(file_get_contents("php://input"));

if ($data) {
    // Mendapatkan matkul_id dari data yang diterima
    $matkul_id = $data->matkul_id;

    // Tentukan status kehadiran (Anda perlu menambahkan logika sesuai dengan kode QR)
    $status = 'hadir'; // Contoh: Status diatur menjadi 'hadir'

    // Ambil data mahasiswa (ganti dengan data sesuai dengan sesi login mahasiswa)
    $id_mahasiswa = 1; // Contoh: ID mahasiswa yang diambil dari sesi login

    // Masukkan data absensi ke dalam database
    $insertQuery = "INSERT INTO absensi (id_mahasiswa, id_qr_code, waktu_absensi, status) VALUES ($id_mahasiswa, $matkul_id, NOW(), '$status')";
    $conn->query($insertQuery);

    // Respon berhasil (atau pesan lain jika diperlukan)
    $response = ['status' => 'success', 'message' => 'Data absensi berhasil disimpan.'];

    echo json_encode($response);
} else {
    // Respon gagal jika data tidak valid
    $response = ['status' => 'error', 'message' => 'Data absensi tidak valid.'];

    echo json_encode($response);
}
?>
