<?php
// Include necessary configurations and header
require_once '../include/config.php';
include 'assets/navbar.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Mata Kuliah <?php echo $username; ?></h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Matkul</th>
                            <th>Nama Matkul</th>
                            <th>Dosen Pengajar</th>
                            <th>Kelas</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mengambil mata kuliah yang diambil oleh mahasiswa
                        $queryMatkul = "SELECT M.id, M.kode_matkul, M.nama_matkul, M.kelas, M.hari, M.jam_mulai, M.jam_selesai, D.nama_dosen
                            FROM matkul M
                            INNER JOIN krs K ON M.id = K.id_matkul
                            INNER JOIN mahasiswa Mah ON K.id_mahasiswa = Mah.id
                            INNER JOIN dosen D ON M.id_dosen = D.id
                            WHERE Mah.id = $idMahasiswa";

                        $resultMatkul = $conn->query($queryMatkul);

                        if ($resultMatkul->num_rows > 0) {
                            $no = 1;
                            while ($rowMatkul = $resultMatkul->fetch_assoc()) {
                                $idMatkul = $rowMatkul["id"];
                                $kodeMatkul = $rowMatkul["kode_matkul"];
                                $namaMatkul = $rowMatkul["nama_matkul"];
                                $dosenPengajar = $rowMatkul["nama_dosen"];
                                $kelas = $rowMatkul["kelas"];
                                $hari = $rowMatkul["hari"];
                                $jamMulai = $rowMatkul["jam_mulai"];
                                $jamSelesai = $rowMatkul["jam_selesai"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $kodeMatkul . "</td>";
                                echo "<td>" . $namaMatkul . "</td>";
                                echo "<td>" . $dosenPengajar . "</td>";
                                echo "<td>" . $kelas . "</td>";
                                echo "<td>" . $hari . "</td>";
                                echo "<td>" . $jamMulai . "</td>";
                                echo "<td>" . $jamSelesai . "</td>";
                                echo "<td>
                                    <a href='detail_absensi.php?matkul_id=$idMatkul' class='btn btn-info btn-sm'>
                                        <i class='far fa-eye'></i>
                                    </a>
                                    <a href='mahasiswa_absensi.php?matkul_id=$idMatkul' class='btn btn-primary btn-sm'>
                                        <i class='fas fa-qrcode'></i>
                                    </a>
                                </td>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>Anda belum mengambil mata kuliah.</td></tr>";
                        }

                        // Tutup koneksi database
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'assets/footer.php'; ?>
