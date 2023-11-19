<?php
require_once '../include/config.php';
require_once '../phpqrcode/qrlib.php';
include 'assets/navbar.php';

// Check if the matkul_id parameter is set in the URL
if (isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];

    // Query to get matkul information based on matkul_id
    $queryMatkul = "SELECT kode_matkul, nama_matkul, id_dosen FROM matkul WHERE id = $matkul_id";
    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul["kode_matkul"];
        $namaMatkul = $rowMatkul["nama_matkul"];
        $dosenID = $rowMatkul["id_dosen"];

        // Generate QR code content
        $qrCodeContent = "Matkul: $kodeMatkul - $namaMatkul - DosenID: $dosenID";

        // Generate QR code image
        $qrCodePath = "../qrcodes/qrcode_$matkul_id.png";
        QRcode::png($qrCodeContent, $qrCodePath);

        // Insert data into absensi table
        $insertAbsensiQuery = "INSERT INTO absensi (id_matkul, id_dosen, tanggal, qr_code) VALUES ($matkul_id, $dosenID, CURDATE(), '$qrCodeContent')";
        $conn->query($insertAbsensiQuery);

        // Display the QR code image
        echo "<div class='container-fluid'>";
        echo "<h1 class='h3 mb-2 text-gray-800'>QR Code Generator</h1>";
        echo "<div class='alert alert-success' role='alert'>";
        echo "QR Code successfully generated and associated with the class.";
        echo "<br><br>";
        echo "<img src='$qrCodePath' alt='QR Code'>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "Invalid matkul_id.";
    }
} else {
    echo "Missing matkul_id parameter.";
}

// Close the database connection
$conn->close();
include 'assets/footer.php';
?>
