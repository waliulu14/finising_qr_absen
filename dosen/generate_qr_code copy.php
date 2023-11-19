<?php
ob_start(); // Start output buffering

// Include configuration file and QR Code library
require_once '../include/config.php';
require_once 'phpqrcode/qrlib.php';
require_once 'assets/navbar.php';

// Set PHP error display
error_reporting(0);

// Function to sanitize input data
function sanitizeInput($data)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Get matkul_id parameter from the URL
if (isset($_GET['matkul_id'])) {
    $matkul_id = sanitizeInput($_GET['matkul_id']);

    // Check if the stop button is clicked
    if (isset($_POST['stop_generator'])) {
        // Include configuration file and connect to the database
        require_once '../include/config.php';

        // Delete the old QR code file if it exists
        $oldQrCodePath = '../qrcodes/' . 'qr_code_' . $matkul_id . '.png';
        if (file_exists($oldQrCodePath)) {
            unlink($oldQrCodePath);
        }

        // Delete the corresponding record in qr_code_absensi table
        $deleteQrCodeQuery = "DELETE FROM qr_code_absensi WHERE id_matkul = $matkul_id";
        $conn->query($deleteQrCodeQuery);

        // Redirect to the signature page
        header('Location: signature_page.php?matkul_id=' . $matkul_id);
        exit;
    }

    // Query to get information about the course based on matkul_id
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

        // Save QR code value to the database
        $queryInsertQRCode = "INSERT INTO qr_code_absensi (qr_code_value, id_dosen, id_matkul) VALUES ('$qr_code_value', $id_dosen, $matkul_id)";
        $resultInsert = $conn->query($queryInsertQRCode);

        if ($resultInsert) {
            // Generate QR code
            $qrCodePath = '../qrcodes/' . 'qr_code_' . $matkul_id . '.png';
            QRcode::png($qr_code_value, $qrCodePath, QR_ECLEVEL_H, 8);

            // Query to get the names of students taking this course
            $queryMahasiswa = "SELECT M.nama_mahasiswa FROM mahasiswa M 
                                INNER JOIN krs K ON M.id = K.id_mahasiswa 
                                WHERE K.id_matkul = $matkul_id";
            $resultMahasiswa = $conn->query($queryMahasiswa);

            echo "<div class='container mt-5'>";
            echo "<div class='row'>";

            // Left section for QR code
            echo "<div class='col-md-6 text-center'>";
            echo "<h2 class='mb-4'>QR Code for Course: $namaMatkul</h2>";
            echo "<img src='$qrCodePath' alt='QR Code' />";
            echo "</div>";

            // Right section for student names in a table
            echo "<div class='col-md-6'>";
            echo "<table id='mahasiswaTable' class='table'>";
            echo "<thead><tr><th>Student Name</th></tr></thead>";
            echo "<tbody>";
            echo '<form method="post" action="">
                    <input type="submit" name="stop_generator" value="Stop Generator">
                </form>';

            // Query to get the names and attendance status of students
            $queryMahasiswa = "SELECT M.nama_mahasiswa, AM.id AS absensi_id FROM mahasiswa M 
                                INNER JOIN krs K ON M.id = K.id_mahasiswa 
                                LEFT JOIN absensi_mahasiswa AM ON M.id = AM.id_mahasiswa AND AM.id_matkul = $matkul_id
                                WHERE K.id_matkul = $matkul_id
                                ORDER BY AM.id DESC, M.nama_mahasiswa"; // Sorting by attendance ID in descending order

            $resultMahasiswa = $conn->query($queryMahasiswa);

            if ($resultMahasiswa && $resultMahasiswa->num_rows > 0) {
                $jumlahAbsensi = 0; // Variable to count the number of students who have attended

                while ($rowMahasiswa = $resultMahasiswa->fetch_assoc()) {
                    $namaMahasiswa = $rowMahasiswa["nama_mahasiswa"];
                    $absensiId = $rowMahasiswa["absensi_id"];

                    // Check if the student has attended
                    if (!empty($absensiId)) {
                        // Student has attended, give green color
                        echo "<tr class='sudah-absen'><td>$namaMahasiswa (Attended)</td></tr>";
                        $jumlahAbsensi++; // Increment the attendance count
                    } else {
                        // Student has not attended
                        echo "<tr><td>$namaMahasiswa</td></tr>";
                    }
                }
                // Display the number of students who have attended in a box
                echo "<div class='info-box'>";
                echo "<div id='jumlahAbsensi'>Number of Students who Attended: " . $jumlahAbsensi . "</div>";
                echo "</div>";

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<tr><td>No students are taking this course.</td></tr>";
            }

            echo "</div>";

            echo "</div>";

            echo "</div>"; // End row
            echo "</div>"; // End container

            // Include footer
            include 'assets/footer.php';

            // Add JavaScript for dynamic color change without reloading
            echo "<script>
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
                                        window.location.href = 'process_attendance.php?qr_content=' + encodeURIComponent(content) + '&matkul_id=<?php echo $matkul_id; ?>';
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

                    // Add JavaScript to dynamically update attendance status
                    function updateAttendanceStatus() {
                        var table = document.getElementById('mahasiswaTable');
                        var rows = table.getElementsByTagName('tr');

                        // Loop through the table rows (starting from index 1 to skip the header)
                        for (var i = 1; i < rows.length; i++) {
                            var currentRow = rows[i];
                            var cells = currentRow.getElementsByTagName('td');

                            // Get the student name from the first cell
                            var namaMahasiswa = cells[0].innerText.split(' ')[0];

                            // Send AJAX request to check attendance status
                            var xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function () {
                                if (xhr.readyState == 4 && xhr.status == 200) {
                                    var response = xhr.responseText;

                                    // Update row color based on the server response
                                    if (response.trim() === '1') {
                                        currentRow.classList.add('sudah-absen'); // Add class for green color
                                    }
                                }
                            };
                            xhr.open('GET', 'check_attendance_status.php?matkul_id=<?php echo $matkul_id; ?>&nama_mahasiswa=' + encodeURIComponent(namaMahasiswa), true);
                            xhr.send();
                        }
                    }

                    // Call the updateAttendanceStatus function for the first time
                    updateAttendanceStatus();

                    // Refresh the page every 5 seconds
                    setInterval(function() {
                        updateAttendanceStatus(); // Update attendance status
                        location.reload(); // Reload the page
                    }, 5000);
                </script>";
        } else {
            echo "Failed to save QR code to the database.";
        }
    } else {
        echo "Course not found.";
    }
} else {
    echo "Invalid matkul_id parameter.";
}

// Stop output buffering and send output to the browser
ob_end_flush();
?>



<style>
    .info-box {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f5f5f5;
    }

    #jumlahAbsensi {
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }

    table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr.sudah-absen {
        background-color: #b6e7c9;
        /* Ganti warna hijau sesuai kebutuhan Anda */
    }

    tr:hover {
        background-color: #f5f5f5;
    }
</style>