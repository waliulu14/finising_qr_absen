<?php
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

    // Check if the QR code for this matkul_id exists
    $qrCodePath = '../qrcodes/' . 'qr_code_' . $matkul_id . '.png';

    if (file_exists($qrCodePath)) {
        // Delete the old QR code file if it exists
        unlink($qrCodePath);

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

            // Generate updated QR code value
            $updated_qr_code_value = "Matkul: $kodeMatkul\nNama: $namaMatkul\nKelas: $kelas\nHari: $hari\nJam: $jamMulai - $jamSelesai\nDosen: $id_dosen";

            // Update the QR code value in the database
            $updateQRCodeQuery = "UPDATE qr_code_absensi SET qr_code_value = '$updated_qr_code_value' WHERE id_matkul = $matkul_id";
            $conn->query($updateQRCodeQuery);

            // Generate updated QR code
            QRcode::png($updated_qr_code_value, $qrCodePath, QR_ECLEVEL_H, 8);

            // Redirect back to generate_qr_code.php
            header("Location: generate_qr_code.php?matkul_id=$matkul_id");
            exit();
        } else {
            echo "Course not found.";
        }
    } else {
        echo "QR Code not found.";
    }
} else {
    echo "Invalid matkul_id parameter.";
}

// Include footer
include 'assets/footer.php';
?>
