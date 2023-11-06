<?php include 'assets/navbar.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Mata Kuliah</h1>
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
                            <th>Kelas</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Dosen Pengajar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../include/config.php';

                        // Query to retrieve matkul data for the currently logged-in dosen
                        $queryMatkul = "SELECT M.id, M.kode_matkul, M.nama_matkul, M.kelas, M.hari, M.jam_mulai, M.jam_selesai, D.nama_dosen
                            FROM matkul M
                            INNER JOIN dosen D ON M.id_dosen = D.id
                            WHERE D.nama_dosen = '$namaDosen'";

                        $resultMatkul = $conn->query($queryMatkul);

                        if ($resultMatkul->num_rows > 0) {
                            $no = 1;
                            while ($rowMatkul = $resultMatkul->fetch_assoc()) {
                                $kodeMatkul = $rowMatkul["kode_matkul"];
                                $namaMatkul = $rowMatkul["nama_matkul"];
                                $kelas = $rowMatkul["kelas"];
                                $hari = $rowMatkul["hari"];
                                $jamMulai = $rowMatkul["jam_mulai"];
                                $jamSelesai = $rowMatkul["jam_selesai"];
                                $dosenPengajar = $rowMatkul["nama_dosen"];
                                $idMatkul = $rowMatkul["id"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $kodeMatkul . "</td>";
                                echo "<td>" . $namaMatkul . "</td>";
                                echo "<td>" . $kelas . "</td>";
                                echo "<td>" . $hari . "</td>";
                                echo "<td>" . $jamMulai . "</td>";
                                echo "<td>" . $jamSelesai . "</td>";
                                echo "<td>" . $dosenPengajar . "</td>";
                                echo "<td>
                                    <a href='lihat_mahasiswa.php?matkul_id=$idMatkul' class='btn btn-info btn-sm'>
                                        <i class='far fa-eye'></i>
                                    </a>
                                    <a href='generate_qr_code.php?matkul_id=$idMatkul' class='btn btn-primary btn-sm'>
                                        <i class='fas fa-qrcode'></i>
                                    </a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>Tidak ada data mata kuliah.</td></tr>";
                        }

                        // Close the database connection
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'assets/footer.php'; ?>
