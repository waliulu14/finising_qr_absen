<?php
require_once '../include/config.php';
include '../phpqrcode/qrlib.php';
include 'assets/navbar.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];
    $queryMatkul = "SELECT kode_matkul, nama_matkul FROM matkul WHERE id = $matkul_id";
    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows > 0) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul['kode_matkul'];
        $namaMatkul = $rowMatkul['nama_matkul'];

        echo "<div class='text-center' >"; // Menambahkan class 'text-center' untuk mengatur teks dan tombol ke tengah
        echo "<h1 class='h3 mb-2 text-gray-800'>Start QR Code for Matkul: $kodeMatkul - $namaMatkul</h1>";
        echo "<div class='container my-5' style='background-color: white; height: 300px; width: 800px;'></div>";
        ?>
        <form method="post" action="generate_qr_code_process.php">
            <input type="hidden" name="matkul_id" value="<?= $matkul_id ?>">
            <button type="submit" class="btn btn-primary mx-auto" name="start_qr_code">Start QR Code</button>
        </form>
        <?php
        echo "</div>";
    } else {
        echo "Course not found.";
    }
}
?>

<br>
</div>

<style>
    #content{
        background-color: #DADDF6;
    }
    .container{
        border-radius: 30px;
    }
</style>

<?php include 'assets/footer.php' ?>
