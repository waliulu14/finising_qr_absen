<?php
require_once '../include/config.php';
include 'assets/navbar.php';

// Function to handle errors
function handle_error($message) {
    echo "<div class='container-fluid'>";
    echo "<h1 class='h3 mb-2 text-gray-800'>Attendance Process</h1>";
    echo "<div class='alert alert-danger' role='alert'>";
    echo $message;
    echo "</div>";
    echo "</div>";
    exit();
}

// Check if the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
}

// Get the student ID based on the logged-in user
$username = $_SESSION['username'];
$queryMahasiswa = "SELECT id FROM mahasiswa WHERE id_user = (SELECT id FROM user WHERE username = ?)";
$stmtMahasiswa = $conn->prepare($queryMahasiswa);
$stmtMahasiswa->bind_param("s", $username);
$stmtMahasiswa->execute();
$resultMahasiswa = $stmtMahasiswa->get_result();

if ($resultMahasiswa->num_rows > 0) {
    $rowMahasiswa = $resultMahasiswa->fetch_assoc();
    $idMahasiswa = $rowMahasiswa["id"];
} else {
    // Handle if student data is not found
    handle_error("Error: Student data not found.");
}

// Check if the qr_content parameter is set in the URL
if (isset($_GET['qr_content'])) {
    $qrContent = $_GET['qr_content'];

    // Avoid SQL injection by using prepared statements
    $queryMatkul = "SELECT id, id_dosen FROM matkul WHERE CONCAT(kode_matkul, '-', nama_matkul, '- DosenID:', id_dosen) = ?";
    
    $stmtMatkul = $conn->prepare($queryMatkul);

    // Check for errors in statement preparation
    if (!$stmtMatkul) {
        handle_error("Error in preparing statement: " . $conn->error);
    }

    $stmtMatkul->bind_param("s", $qrContent);

    // Check for errors in binding parameters
    if (!$stmtMatkul) {
        handle_error("Error in binding parameters: " . $stmtMatkul->error);
    }

    $stmtMatkul->execute();

    // Check for errors in statement execution
    if (!$stmtMatkul) {
        handle_error("Error in executing statement: " . $stmtMatkul->error);
    }

    $resultMatkul = $stmtMatkul->get_result();

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $idMatkul = $rowMatkul["id"];
        $idDosen = $rowMatkul["id_dosen"];

        // Check if the student has already attended
        $checkAttendanceQuery = "SELECT id FROM absensi WHERE qr_code = ? AND id_mahasiswa = ?";
        $stmtCheckAttendance = $conn->prepare($checkAttendanceQuery);

        // Check for errors in statement preparation
        if (!$stmtCheckAttendance) {
            handle_error("Error in preparing statement: " . $conn->error);
        }

        $stmtCheckAttendance->bind_param("si", $qrContent, $idMahasiswa);

        // Check for errors in binding parameters
        if (!$stmtCheckAttendance) {
            handle_error("Error in binding parameters: " . $stmtCheckAttendance->error);
        }

        $stmtCheckAttendance->execute();

        // Check for errors in statement execution
        if (!$stmtCheckAttendance) {
            handle_error("Error in executing statement: " . $stmtCheckAttendance->error);
        }

        $resultCheckAttendance = $stmtCheckAttendance->get_result();

        if ($resultCheckAttendance->num_rows > 0) {
            // If attended, display a message
            echo "<div class='container-fluid'>";
            echo "<h1 class='h3 mb-2 text-gray-800'>Attendance Process</h1>";
            echo "<div class='alert alert-info' role='alert'>";
            echo "You have already attended this class.";
            echo "</div>";
            echo "</div>";
        } else {
            // If not attended, record attendance
            $insertAttendanceQuery = "INSERT INTO absensi (id_matkul, id_dosen, id_mahasiswa, tanggal, qr_code) 
                                      VALUES (?, ?, ?, CURDATE(), ?)";
            $stmtInsertAttendance = $conn->prepare($insertAttendanceQuery);

            // Check for errors in statement preparation
            if (!$stmtInsertAttendance) {
                handle_error("Error in preparing statement: " . $conn->error);
            }

            $stmtInsertAttendance->bind_param("iiis", $idMatkul, $idDosen, $idMahasiswa, $qrContent);

            // Check for errors in binding parameters
            if (!$stmtInsertAttendance) {
                handle_error("Error in binding parameters: " . $stmtInsertAttendance->error);
            }

            if ($stmtInsertAttendance->execute()) {
                echo "<div class='container-fluid'>";
                echo "<h1 class='h3 mb-2 text-gray-800'>Attendance Process</h1>";
                echo "<div class='alert alert-success' role='alert'>";
                echo "Attendance successfully recorded!";
                echo "</div>";
                echo "</div>";
            } else {
                handle_error("Error recording attendance: " . $stmtInsertAttendance->error);
            }
        }
    } else {
        // Display an error message for invalid QR code content
        handle_error("Invalid QR code content.");
    }
} else {
    // Display an error message if the qr_content parameter is missing
    handle_error("QR code content parameter is missing.");
}

// Include the footer
include 'assets/footer.php';
?>
