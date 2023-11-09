<?php
// Include the necessary PHP files and initialize the database connection
require_once '../include/config.php';
include 'assets/navbar.php';

// Inisialisasi variabel
$matkulId = null;
$error = '';

// Ambil id matkul dari parameter GET
if (isset($_GET['matkul_id'])) {
    $matkulId = $_GET['matkul_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Periksa apakah "tanda_tangan" ada dalam array POST
    if (isset($_POST['tanda_tangan'])) {
        $tandaTangan = $_POST['tanda_tangan'];

        // Validasi tanda tangan (pastikan tidak kosong)
        if (empty($tandaTangan)) {
            $error = 'Tanda tangan harus diisi.';
        } else {
            // Simpan tanda tangan ke database (contoh: tabel "tanda_tangan_dosen")
            $queryInsertTandaTangan = "INSERT INTO tanda_tangan_dosen (id_dosen, id_matkul, tgl_tanda_tangan, tanda_tangan) 
                                      VALUES (1, $matkulId, NOW(), ?)";

            $stmt = $conn->prepare($queryInsertTandaTangan);
            $stmt->bind_param("s", $tandaTangan);

            if ($stmt->execute()) {
                // Tanda tangan berhasil disimpan. Anda dapat melakukan operasi tambahan jika diperlukan.

                // Setelah berhasil menyimpan tanda tangan, arahkan pengguna ke halaman "simpan_tanda_tangan.php"
                header('Location: simpan_tanda_tangan.php');
                exit;
            } else {
                $error = 'Gagal menyimpan tanda tangan.';
            }
        }
    } else {
        $error = 'Tanda tangan tidak ditemukan dalam permintaan POST.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Tanda Tangan Absensi</title>
</head>
<body>
    <div class="container">
        <h1>Tanda Tangan Absensi</h1>
        <?php
        if ($matkulId !== null) {
            echo '<p>Mata Kuliah ID: ' . $matkulId . '</p>';
        } else {
            echo '<p>Mata Kuliah ID tidak valid.</p>';
        }
        ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="tanda_tangan">Tanda Tangan Dosen:</label>
                <!-- Menambahkan HTML5 canvas untuk tanda tangan -->
                <canvas id="signatureCanvas" name="tanda_tangan" width="500" height="200"></canvas>
            </div>
            <button type="submit" class="btn btn-primary">Tandatangani</button>
        </form>
    </div>

    <!-- Sisipkan pustaka Signature Pad JavaScript (dalam hal ini, menggunakan URL dari pustaka online) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
    <script>
        // Inisialisasi Signature Pad
        var canvas = document.getElementById('signatureCanvas');
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        // Tangani pengiriman formulir
        document.querySelector('form').addEventListener('submit', function(e) {
            // Simpan tanda tangan dalam format base64 ke elemen input tersembunyi
            var tandaTanganInput = document.createElement('input');
            tandaTanganInput.name = 'tanda_tangan';
            tandaTanganInput.type = 'hidden';
            tandaTanganInput.value = signaturePad.toDataURL();

            // Tambahkan input tanda tangan ke formulir
            this.appendChild(tandaTanganInput);
        });
    </script>
</body>
</html>
<?php include 'assets/footer.php'; ?>
