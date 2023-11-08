<?php include 'assets/navbar.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Detail Absensi</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Waktu Absensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil parameter matkul_id dari URL
                        $matkul_id = $_GET['matkul_id'];

                        // Query untuk mengambil data absensi untuk mata kuliah tertentu
                        $queryAbsensi = "SELECT A.id, M.nama_mahasiswa, A.waktu_absensi
                            FROM absensi A
                            INNER JOIN mahasiswa M ON A.id_mahasiswa = M.id
                            WHERE A.id_qr_code = $matkul_id";

                        $resultAbsensi = $conn->query($queryAbsensi);

                        if ($resultAbsensi->num_rows > 0) {
                            $no = 1;
                            while ($rowAbsensi = $resultAbsensi->fetch_assoc()) {
                                $namaMahasiswa = $rowAbsensi["nama_mahasiswa"];
                                $waktuAbsensi = $rowAbsensi["waktu_absensi"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $namaMahasiswa . "</td>";
                                echo "<td>" . $waktuAbsensi . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Tidak ada data absensi untuk mata kuliah ini.</td></tr>";
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
