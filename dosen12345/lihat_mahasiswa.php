<?php


require_once '../include/config.php';

// Ambil ID mata kuliah dari parameter
if (isset($_GET['matkul_id'])) {
    $matkul_id = $_GET['matkul_id'];

    // Query untuk mendapatkan informasi mata kuliah
    $queryMatkul = "SELECT M.kode_matkul, M.nama_matkul, D.nama_dosen
                    FROM matkul M
                    INNER JOIN dosen D ON M.id_dosen = D.id
                    WHERE M.id = $matkul_id";

    $resultMatkul = $conn->query($queryMatkul);

    if ($resultMatkul->num_rows == 1) {
        $rowMatkul = $resultMatkul->fetch_assoc();
        $kodeMatkul = $rowMatkul['kode_matkul'];
        $namaMatkul = $rowMatkul['nama_matkul'];
        $namaDosen = $rowMatkul['nama_dosen'];
    } else {
        // Handle jika mata kuliah tidak ditemukan
        echo "Mata Kuliah tidak ditemukan.";
        exit();
    }

    // Query untuk mendapatkan daftar mahasiswa yang terdaftar dalam mata kuliah
    $queryMahasiswa = "SELECT M.nama_mahasiswa, M.nim, M.email, M.milis
                        FROM mahasiswa M
                        INNER JOIN krs K ON M.id = K.id_mahasiswa
                        WHERE K.id_matkul = $matkul_id";

    $resultMahasiswa = $conn->query($queryMahasiswa);
} else {
    // Handle jika parameter tidak ada
    echo "Parameter mata kuliah tidak valid.";
    exit();
}

// Include file header
include 'assets/navbar.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Mahasiswa dalam Mata Kuliah: <?php echo $kodeMatkul; ?></h1>
    <p><strong>Nama Matkul:</strong> <?php echo $namaMatkul; ?></p>
    <p><strong>Dosen Pengajar:</strong> <?php echo $namaDosen; ?></p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Milis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultMahasiswa->num_rows > 0) {
                            $no = 1;
                            while ($rowMahasiswa = $resultMahasiswa->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $rowMahasiswa['nama_mahasiswa'] . "</td>";
                                echo "<td>" . $rowMahasiswa['nim'] . "</td>";
                                echo "<td>" . $rowMahasiswa['email'] . "</td>";
                                echo "<td>" . $rowMahasiswa['milis'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tidak ada mahasiswa terdaftar dalam mata kuliah ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'assets/footer.php'; ?>
