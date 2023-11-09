<?php
include 'assets/navbar.php';
require_once '../include/config.php';
include '../phpqrcode/qrlib.php';
?>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['matkul_id'])) {
                    $matkul_id = $_GET['matkul_id'];
                    $queryMatkul = "SELECT kode_matkul, nama_matkul FROM matkul WHERE id = $matkul_id";
                    $resultMatkul = $conn->query($queryMatkul);

                    if ($resultMatkul->num_rows > 0) {
                        $rowMatkul = $resultMatkul->fetch_assoc();
                        $kodeMatkul = $rowMatkul['kode_matkul'];
                        $namaMatkul = $rowMatkul['nama_matkul'];

                        echo "<h1 class='h3 mb-2 text-gray-800'>Start QR Code for Matkul: $kodeMatkul - $namaMatkul</h1>";
                        ?>
                        <form method="post" action="generate_qr_code.php">
                            <input type="hidden" name="matkul_id" value="<?= $matkul_id ?>">
                            <button type="submit" class="btn btn-primary" name="start_qr_code">Start QR Code</button>
                        </form>
                        <?php
                    } else {
                        echo "Course not found.";
                    }
                }
                ?>
            </div>
           <div class="col-md-6">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_qr_code'])) {
        $matkul_id = $_POST['matkul_id'];
        $queryMatkul = "SELECT kode_matkul, nama_matkul FROM matkul WHERE id = $matkul_id";
        $resultMatkul = $conn->query($queryMatkul);

        if ($resultMatkul->num_rows > 0) {
            $rowMatkul = $resultMatkul->fetch_assoc();
            $kodeMatkul = $rowMatkul['kode_matkul'];
            $namaMatkul = $rowMatkul['nama_matkul'];

            // Generate QR Code data
            $qrCodeData = "Matkul: $kodeMatkul - $namaMatkul\n";
            $qrCodeData .= "Dosen: $namaDosen\n";
            $qrCodeData .= "Waktu: " . date('Y-m-d H:i:s') . "\n";

            // Save QR Code data to the database
            $insertQuery = "INSERT INTO qr_code (id_dosen, id_matkul, tgl_pencetakan, qr_code) VALUES ((SELECT id FROM dosen WHERE nama_dosen = '$namaDosen'), $matkul_id, NOW(), '$qrCodeData')";
            $conn->query($insertQuery);

            // Location to save the QR Code image
            $qrCodePath = 'qr_codes/' . $matkul_id . '.png';

            QRcode::png($qrCodeData, $qrCodePath);

            echo "<h1 class='h3 mb-2 text-gray-800'>QR Code for Matkul: $kodeMatkul - $namaMatkul</h1>";
            echo "<img src='qr_codes/$matkul_id.png' alt='QR Code'>";
            echo "<br>";
            echo "<a href='qr_codes/$matkul_id.png' download='qr_code.png' class='btn btn-success'>Download QR Code</a>";

            // Display the list of students
            $studentQuery = "SELECT m.nama_mahasiswa, m.nim FROM mahasiswa m
                             JOIN krs k ON m.id = k.id_mahasiswa
                             WHERE k.id_matkul = $matkul_id";
            $resultStudents = $conn->query($studentQuery);

            if ($resultStudents->num_rows > 0) {
                echo "<h2>Students Enrolled:</h2>";
                echo "<ul>";
                while ($rowStudent = $resultStudents->fetch_assoc()) {
                    $namaMahasiswa = $rowStudent['nama_mahasiswa'];
                    $nim = $rowStudent['nim'];
                    echo "<li>$namaMahasiswa (NIM: $nim)</li>";
                }
                echo "</ul>";
            } else {
                echo "No students found for this course.";
            }
        } else {
            echo "Course not found.";
        }
    }
    ?>
</div>


            
        </div>
    </div>
    <?php include 'assets/footer.php'?>
