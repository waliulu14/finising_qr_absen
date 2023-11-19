<?php
require_once '../include/config.php';
require_once '../phpqrcode/qrlib.php';
include 'assets/navbar.php';

// Check if the matkul_id parameter is set in the URL
if (isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];

    // Query to get matkul information based on matkul_id
    $queryMatkul = "SELECT kode_matkul, nama_matkul FROM matkul WHERE id = $matkul_id";
    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul["kode_matkul"];
        $namaMatkul = $rowMatkul["nama_matkul"];

        // Generate QR code content (you can customize this as needed)
        $qrCodeContent = "Matkul: $kodeMatkul - $namaMatkul";

        // Generate QR code image
        $qrCodePath = "../qrcodes/qrcode_$matkul_id.png";
        QRcode::png($qrCodeContent, $qrCodePath);

        // Display the QR code image
        echo "<img src='$qrCodePath' alt='QR Code'>";
    } else {
        echo "Invalid matkul_id.";
    }
} else {
    echo "Missing matkul_id parameter.";
}

// Close the database connection
$conn->close();
?>
<?php include 'assets/footer.php'; ?>
