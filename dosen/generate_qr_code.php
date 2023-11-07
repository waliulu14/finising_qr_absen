<?php
require_once 'assets/navbar.php'; // Sesuaikan dengan lokasi file navbar Anda
require_once '../include/config.php';
require_once '../vendor/autoload.php';

use Endroid\QrCode\QrCode;

// Ambil matkul_id dari parameter GET
if (isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];

    // Query untuk mendapatkan detail mata kuliah berdasarkan matkul_id
    $queryMatkul = "SELECT M.kode_matkul, M.nama_matkul, D.nama_dosen
                    FROM matkul M
                    INNER JOIN dosen D ON M.id_dosen = D.id
                    WHERE M.id = $matkul_id";

    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul['kode_matkul'];
        $namaMatkul = $rowMatkul['nama_matkul'];
        $dosenPengajar = $rowMatkul['nama_dosen'];
    } else {
        echo "Mata kuliah tidak ditemukan.";
        exit();
    }
} else {
    echo "Parameter matkul_id tidak valid.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];

    // Buat informasi pertemuan
    $informasiPertemuan = "Mata Kuliah: $kodeMatkul - $namaMatkul\nDosen Pengajar: $dosenPengajar\nTanggal: $tanggal\nWaktu: $waktu\nLokasi: $lokasi";

    // Generate QR Code
    $qrCode = new QrCode($informasiPertemuan);

    // Simpan QR Code ke dalam database bersama dengan informasi pertemuan
    $querySimpanQRCode = "INSERT INTO qr_code (id_dosen, id_matkul, tgl_pencetakan, qr_code) VALUES ('$idDosen', '$matkul_id', '$tanggal', '" . $qrCode->writeDataUri() . "')";
    if ($conn->query($querySimpanQRCode) === TRUE) {
        // Tampilkan QR Code kepada dosen
        echo '<img src="' . $qrCode->writeDataUri() . '" />';
        echo "<br>QR Code telah berhasil dicetak.";
    } else {
        echo "Gagal menyimpan QR Code.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate QR Code Absensi</title>
</head>
<body>
    <h1>Generate QR Code Absensi untuk Mata Kuliah: <?php echo $kodeMatkul; ?> - <?php echo $namaMatkul; ?></h1>

    <form method="post">
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" required><br>

        <label for "waktu">Waktu:</label>
        <input type="time" name="waktu" required><br>

        <label for="lokasi">Lokasi:</label>
        <input type="text" name="lokasi" required><br>

        <input type="submit" value="Generate QR Code">
    </form>

    <!-- Tambahkan tautan atau tombol untuk kembali ke halaman sebelumnya atau riwayat pertemuan kelas -->

</body>
</html>
