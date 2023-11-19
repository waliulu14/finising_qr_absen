<?php
// Include necessary configurations
require_once '../include/config.php';
include 'assets/navbar.php';

// Check if the user is not logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: ../login.php');
    exit(); // Stop script execution
}

// Ambil data QR code dari URL
if (isset($_GET['qr_content']) && isset($_GET['matkul_id']) && isset($_GET['id_mahasiswa'])) {
    $qr_content = urldecode($_GET['qr_content']);
    $matkul_id = $_GET['matkul_id'];
    $id_mahasiswa = $_GET['id_mahasiswa'];

    // Query untuk mendapatkan informasi mata kuliah berdasarkan matkul_id
    $queryMatkul = "SELECT kode_matkul, nama_matkul, kelas, hari, jam_mulai, jam_selesai, id_dosen FROM matkul WHERE id = $matkul_id";
    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul && $resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul["kode_matkul"];
        $namaMatkul = $rowMatkul["nama_matkul"];
        $kelas = $rowMatkul["kelas"];
        $hari = $rowMatkul["hari"];
        $jamMulai = $rowMatkul["jam_mulai"];
        $jamSelesai = $rowMatkul["jam_selesai"];
        $id_dosen = $rowMatkul["id_dosen"];

        // Cek apakah QR code sesuai dengan informasi mata kuliah
        if ($qr_content === "Matkul: $kodeMatkul\nNama: $namaMatkul\nKelas: $kelas\nHari: $hari\nJam: $jamMulai - $jamSelesai\nDosen: $id_dosen") {
            $tanggal = date('Y-m-d');
            
            // Query untuk menyimpan data absensi mahasiswa
            $queryAbsensi = "INSERT INTO absensi_mahasiswa (id_mahasiswa, id_matkul, id_dosen, tanggal, hadir, qr_code_id) VALUES ($id_mahasiswa, $matkul_id, $id_dosen, '$tanggal', TRUE, NULL)";
            $resultAbsensi = $conn->query($queryAbsensi);

            if ($resultAbsensi) {
                echo "Absensi berhasil dicatat.";
                // Use JavaScript to redirect after displaying the success message
                echo "<script>window.location.href='student_signature.php?matkul_id=$matkul_id&id_mahasiswa=$id_mahasiswa';</script>";
                exit();
            } else {
                echo "Gagal mencatat absensi.";
            }
        } else {
            echo "QR code tidak sesuai dengan mata kuliah ini.";
        }
    } else {
        echo "Mata kuliah tidak ditemukan.";
    }
} else {
    echo "Parameter qr_content, matkul_id, atau id_mahasiswa tidak valid.";
}

// Tutup koneksi database
$conn->close();
?>
<?php include 'assets/footer.php'; ?>
