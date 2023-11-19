<?php
// Include necessary configurations and header
require_once '../include/config.php';
include 'assets/navbar.php';

// Menampilkan nama mahasiswa yang sedang login
if (isset($_SESSION['nama_mahasiswa'])) {
    echo "<h2>Selamat datang, " . $_SESSION['nama_mahasiswa'] . "!</h2>";
} else {
    echo "<h2>Selamat datang!</h2>";
}

// Ambil parameter matkul_id dari URL
if (isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];

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

        // Generate QR code value
        $qr_code_value = "Matkul: $kodeMatkul\nNama: $namaMatkul\nKelas: $kelas\nHari: $hari\nJam: $jamMulai - $jamSelesai\nDosen: $id_dosen";

        // Menampilkan informasi mata kuliah
        echo "<div class='container mt-5'>";
        echo "<h2 class='mb-4'>Informasi Mata Kuliah: $namaMatkul</h2>";
        echo "<p>Kode Matkul: $kodeMatkul</p>";
        echo "<p>Kelas: $kelas</p>";
        echo "<p>Hari: $hari</p>";
        echo "<p>Jam: $jamMulai - $jamSelesai</p>";
        echo "<p>Dosen Pengajar: $id_dosen</p>";

        // Menampilkan pemindai QR code
        echo "<div id='scanner-container' class='card shadow mb-4'>";
        echo "<div class='card-body'>";
        echo "<video id='scanner' width='100%'></video>";
        echo "</div>";
        echo "</div>";

        echo "</div>";

        // Include footer
        include 'assets/footer.php';
        ?>
        
        <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Check if the browser supports getUserMedia
                if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {
                    // Access the device camera and start scanning
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                        .then(function (stream) {
                            // Initialize the scanner
                            var scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });

                            // Listen for QR code scans
                            scanner.addListener('scan', function (content) {
                                // Redirect to the attendance processing page with the scanned content
                                window.location.href = 'process_attendance.php?qr_content=' + encodeURIComponent(content) + '&matkul_id=<?php echo $matkul_id; ?>&id_mahasiswa=<?php echo $idMahasiswa; ?>';
                            });

                            // Start scanning
                            Instascan.Camera.getCameras().then(function (cameras) {
                                if (cameras.length > 0) {
                                    scanner.start(cameras[0]);
                                } else {
                                    console.error('No cameras found.');
                                }
                            });
                        })
                        .catch(function (error) {
                            console.error('Error accessing the camera:', error);
                        });
                } else {
                    console.error('getUserMedia is not supported.');
                }
            });
        </script>

        <?php
    } else {
        echo "Mata kuliah tidak ditemukan.";
    }
} else {
    echo "Parameter matkul_id tidak valid.";
}

// Hentikan output buffering dan kirimkan output ke browser
ob_end_flush();
?>
