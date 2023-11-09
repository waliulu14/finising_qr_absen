<?php
require_once '../include/config.php';
include '../phpqrcode/qrlib.php';
include 'assets/navbar.php';

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

        // Set the size of the QR code (e.g., 300x300 pixels)
        QRcode::png($qrCodeData, $qrCodePath, QR_ECLEVEL_L, 10);
        echo "<div class='container'>
                <div class='row'>
                    <div class='col-md-6'>
                        <h1 class='h3 mb-2 text-gray-800'>QR Code for Matkul: $kodeMatkul - $namaMatkul</h1>
                        <img src='qr_codes/$matkul_id.png' alt='QR Code'>
                        <br>
                        <a href='tanda_tangan.php?matkul_id=$matkul_id' class='btn btn-danger'>Stop Generator Code</a>


                    </div>
                    <div class='col-md-6'>
                        <h2>Students Enrolled:</h2>
                        <table class='table table-bordered'>
                            <thead>
                            <tr>
                                    <th>NIM</th>
                                    <th>Nama Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>";

        // Display the list of students
        $studentQuery = "SELECT m.nama_mahasiswa, m.nim FROM mahasiswa m
                        JOIN krs k ON m.id = k.id_mahasiswa
                        WHERE k.id_matkul = $matkul_id";
        $resultStudents = $conn->query($studentQuery);

        if ($resultStudents->num_rows > 0) {
            while ($rowStudent = $resultStudents->fetch_assoc()) {
                $nim = $rowStudent['nim'];
                $namaMahasiswa = $rowStudent['nama_mahasiswa'];

                echo "<tr>";
                echo "<td>$nim</td>";
                echo "<td>$namaMahasiswa</td>";

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No students found for this course.</td></tr>";
        }

        echo "</tbody>
                </table>
            </div>
        </div>
    </div>";
    } else {
        echo "Course not found.";
    }
}
?>

<?php include 'assets/footer.php' ?>



<!-- 
<style>
    #content{
        background-color: #DADDF6;
    }
</style> -->

