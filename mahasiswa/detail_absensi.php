<?php include 'assets/navbar.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Absensi</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Absensi</th>
                            <th>Mata Kuliah</th>
                            <th>Dosen Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../include/config.php';

                        // Ganti ini dengan ID mahasiswa yang sesuai
                      

                        // Query untuk mengambil data absensi mahasiswa
                        $queryAbsensi = "SELECT A.waktu_absensi, M.nama_matkul, D.nama_dosen
                            FROM absensi A
                            INNER JOIN matkul M ON A.id_matkul = M.id
                            INNER JOIN dosen D ON M.id_dosen = D.id
                            WHERE A.id_mahasiswa = $idMahasiswa";

                            

                        $resultAbsensi = $conn->query($queryAbsensi);

                        if ($resultAbsensi->num_rows > 0) {
                            $no = 1;
                            while ($rowAbsensi = $resultAbsensi->fetch_assoc()) {
                                $tanggalAbsensi = $rowAbsensi["waktu_absensi"];
                                $mataKuliah = $rowAbsensi["nama_matkul"];
                                $dosenPengajar = $rowAbsensi["nama_dosen"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $tanggalAbsensi . "</td>";
                                echo "<td>" . $mataKuliah . "</td>";
                                echo "<td>" . $dosenPengajar . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Belum ada data absensi.</td></tr>";
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
