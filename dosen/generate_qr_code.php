<?php
// Include the necessary PHP files and initialize the database connection
require_once '../include/config.php';
include 'assets/navbar.php';
require '../phpqrcode-master/qrlib.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Generate QR Code</title>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <?php
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

                        // Generate QR code content
                        $qrCodeContent = "Mata Kuliah: $kodeMatkul\nNama Mata Kuliah: $namaMatkul\nDosen Pengajar: $dosenPengajar";

                        // Generate QR code image
                        // Generate QR code image with a larger size
                        $qrCodeImage = 'qr_codes/' . $matkulId . '.png';
                        QRcode::png($qrCodeContent, $qrCodeImage, QR_ECLEVEL_L, 10); // The last parameter (10) controls the size

                        // Display QR code
                        echo '<h1>Generate QR Code</h1>';
                        echo "<p>Scan QR Code ini untuk melakukan absensi di mata kuliah <b>$namaMatkul</b></p>";
                        echo '<img src="' . $qrCodeImage . '" alt="QR Code" class="img-fluid">';
                        
                    } else {
                        echo 'Data mata kuliah tidak ditemukan.';
                    }
                } else {
                    echo 'ID mata kuliah tidak valid.';
                }
                ?>
            </div>

            <div class="col-md-6">
                <?php
                // Query to retrieve the "nim" and names of students attending this course
                $queryMahasiswa = "SELECT M.nim, M.nama_mahasiswa
                               FROM krs K
                               INNER JOIN mahasiswa M ON K.id_mahasiswa = M.id
                               WHERE K.id_matkul = $matkulId";
                $resultMahasiswa = $conn->query($queryMahasiswa);

                $mahasiswaList = array();
                while ($rowMahasiswa = $resultMahasiswa->fetch_assoc()) {
                    $mahasiswaList[] = $rowMahasiswa;
                }

                if (empty($mahasiswaList)) {
                    echo 'Tidak ada mahasiswa yang mengikuti mata kuliah ini.';
                } else {
                    // Display Mahasiswa in a table on the right side if there are any
                    echo '<h2>Mahasiswa yang mengikuti:</h2>';
                    echo '<table class="table table-bordered">';
                    echo '<tbody>';
                    foreach ($mahasiswaList as $key => $mahasiswa) {
                        echo "<tr><td>{$mahasiswa['nim']}</td><td>{$mahasiswa['nama_mahasiswa']}</td></tr>";
                    }
                    echo '</tbody>';
                    echo '</table>';
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include 'assets/footer.php'; ?>