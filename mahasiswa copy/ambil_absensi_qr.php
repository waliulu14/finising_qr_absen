<?php
// Include necessary files
include 'assets/navbar.php';

// Check if the matkul_id parameter is set in the URL
if (isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];

    // Query to get matkul data based on matkul_id
    $queryMatkul = "SELECT * FROM matkul WHERE kode_matkul = '$matkul_id'";
    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul["kode_matkul"];
        $namaMatkul = $rowMatkul["nama_matkul"];
        $kelas = $rowMatkul["kelas"];
        $hari = $rowMatkul["hari"];
        $jamMulai = $rowMatkul["jam_mulai"];
        $jamSelesai = $rowMatkul["jam_selesai"];
        $dosenPengajar = $rowMatkul["id_dosen"];
        $idMatkul = $rowMatkul["id"];

        // Generate QR Code content
        $qrCodeContent = "Matkul: $kodeMatkul\nNama: $namaMatkul\nKelas: $kelas\nHari: $hari\nJam: $jamMulai-$jamSelesai\nDosen: $dosenPengajar";

        // Include the QR code library
        include '../phpqrcode/qrlib.php';

        // Generate QR Code and display it
        echo "<div class='container-fluid'>";
        echo "<h1 class='h3 mb-2 text-gray-800'>QR Code for $kodeMatkul</h1>";
        echo "<img src='path/to/store/qrcodes/$idMatkul.png' alt='QR Code for $kodeMatkul' />";
        echo "</div>";

        // Save the QR Code to a file (adjust the path as needed)
        $qrCodePath = "../qrcodes/$idMatkul.png";
        QRcode::png($qrCodeContent, $qrCodePath, 'L', 10, 2);

    } else {
        // Redirect or show an error message if matkul not found
        header("Location: error_page.php");
        exit();
    }

} else {
    // Redirect or show an error message if matkul_id is not set
    header("Location: error_page.php");
    exit();
}

// Include footer
include 'assets/footer.php';
?>
