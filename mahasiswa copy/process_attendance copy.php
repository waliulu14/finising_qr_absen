<?php
// Include necessary configurations and header
require_once '../include/config.php';
include 'assets/navbar.php';

// Check if the qr_content parameter is set in the URL
if (isset($_GET['qr_content'])) {
    $qrContent = $_GET['qr_content'];

    // Escape and sanitize variables to prevent SQL injection
    $qrContent = mysqli_real_escape_string($conn, $qrContent);
    $idMahasiswa = (int)$idMahasiswa;
    $idDosen = (int)$idDosen;

    // Query to get matkul information based on the QR code content
    $queryMatkul = "SELECT M.id, M.kode_matkul, M.nama_matkul, M.kelas, M.hari, M.jam_mulai, M.jam_selesai, D.nama_dosen
                    FROM matkul M
                    INNER JOIN dosen D ON M.id_dosen = D.id
                    WHERE CONCAT('Matkul: ', M.kode_matkul, ' - ', M.nama_matkul) = '$qrContent'";

    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $idMatkul = $rowMatkul["id"];

        // Insert attendance record into the absensi table
        $insertAttendanceQuery = "INSERT INTO absensi (kode_qr, mahasiswa_id, dosen_id, mata_kuliah_id) 
                                  VALUES ('$qrContent', $idMahasiswa, $idDosen, $idMatkul)";

        if ($conn->query($insertAttendanceQuery) === TRUE) {
            // Display success message
            echo "<div class='container-fluid'>";
            echo "<h1 class='h3 mb-2 text-gray-800'>Process Attendance</h1>";
            echo "<div class='alert alert-success' role='alert'>";
            echo "Attendance recorded successfully!";
            echo "</div>";
            echo "</div>";
        } else {
            // Display error message
            echo "<div class='container-fluid'>";
            echo "<h1 class='h3 mb-2 text-gray-800'>Process Attendance</h1>";
            echo "<div class='alert alert-danger' role='alert'>";
            echo "Error recording attendance: " . $conn->error;
            echo "</div>";
            echo "</div>";
        }
    } else {
        // Display error message for invalid QR code content
        echo "<div class='container-fluid'>";
        echo "<h1 class='h3 mb-2 text-gray-800'>Process Attendance</h1>";
        echo "<div class='alert alert-danger' role='alert'>";
        echo "Invalid QR code content.";
        echo "</div>";
        echo "</div>";
    }
} else {
    // Display error message for missing QR code content parameter
    echo "<div class='container-fluid'>";
    echo "<h1 class='h3 mb-2 text-gray-800'>Process Attendance</h1>";
    echo "<div class='alert alert-danger' role='alert'>";
    echo "Missing QR code content parameter.";
    echo "</div>";
    echo "</div>";
}

// Include footer
include 'assets/footer.php';
?>
