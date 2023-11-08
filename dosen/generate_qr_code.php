<?php
// Include the necessary PHP files and initialize the database connection
require_once '../include/config.php';
include 'assets/navbar.php';
require '../phpqrcode-master/qrlib.php';

// Ambil id matkul dari parameter GET
if (isset($_GET['matkul_id'])) {
    $matkulId = $_GET['matkul_id'];

    // Query untuk mendapatkan informasi matkul
    $queryMatkul = "SELECT M.id, M.kode_matkul, M.nama_matkul, D.nama_dosen
                    FROM matkul M
                    INNER JOIN dosen D ON M.id_dosen = D.id
                    WHERE M.id = $matkulId";
    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul['kode_matkul'];
        $namaMatkul = $rowMatkul['nama_matkul'];
        $dosenPengajar = $rowMatkul['nama_dosen'];

        // Query untuk mendapatkan nama-nama mahasiswa yang mengikuti mata kuliah ini
        $queryMahasiswa = "SELECT M.nama_mahasiswa
                           FROM krs K
                           INNER JOIN mahasiswa M ON K.id_mahasiswa = M.id
                           WHERE K.id_matkul = $matkulId";
        $resultMahasiswa = $conn->query($queryMahasiswa);

        $namaMahasiswaList = array();
        while ($rowMahasiswa = $resultMahasiswa->fetch_assoc()) {
            $namaMahasiswaList[] = $rowMahasiswa['nama_mahasiswa'];
        }

        // Generate QR code content
        $qrCodeContent = "Mata Kuliah: $kodeMatkul\nNama Mata Kuliah: $namaMatkul\nDosen Pengajar: $dosenPengajar\nMahasiswa: " . implode(", ", $namaMahasiswaList);

        // Generate QR code image
        $qrCodeImage = 'qr_codes/' . $matkulId . '.png';
        QRcode::png($qrCodeContent, $qrCodeImage);

        // Tampilkan QR code
        echo '<div class="container">';
        echo '<h1>Generate QR Code</h1>';
        echo "<p>Scan QR Code ini untuk melakukan absensi di mata kuliah $kodeMatkul</p>";
        echo '<p>Mahasiswa yang mengikuti: ' . implode(", ", $namaMahasiswaList) . '</p>';
        echo '<img src="' . $qrCodeImage . '" alt="QR Code">';
        echo '</div>';
    } else {
        echo 'Data mata kuliah tidak ditemukan.';
    }
} else {
    echo 'ID mata kuliah tidak valid.';
}

// Tutup koneksi database
$conn->close();

include 'assets/footer.php';
?>
