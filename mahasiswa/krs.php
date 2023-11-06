<?php include 'assets/navbar.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Kartu Rencana Studi (KRS)</h1>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahKRSModal">
        Tambah KRS
    </button>
    <br>
    <br>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Kode Matkul</th>
                            <th>Nama Matkul</th>
                            <th>Kelas</th>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Dosen Pengampu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../include/config.php';

                        // Check if the connection is successful
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Query to retrieve KRS data with related Mahasiswa, Matkul, and Dosen
                        $query = "SELECT K.id, M.nama_mahasiswa, MK.kode_matkul, MK.nama_matkul, MK.kelas, MK.hari, MK.jam_mulai, MK.jam_selesai, D.nama_dosen
                                  FROM krs K
                                  INNER JOIN mahasiswa M ON K.id_mahasiswa = M.id
                                  INNER JOIN matkul MK ON K.id_matkul = MK.id
                                  INNER JOIN dosen D ON MK.id_dosen = D.id";
                       
                       $result = $conn->query($query);
                       if ($result->num_rows > 0) {
                           $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row["nama_mahasiswa"] . "</td>";
                                echo "<td>" . $row["kode_matkul"] . "</td>";
                                echo "<td>" . $row["nama_matkul"] . "</td>";
                                echo "<td>" . $row["kelas"] . "</td>";
                                echo "<td>" . $row["hari"] . "</td>";
                                echo "<td>" . $row["jam_mulai"] . "</td>";
                                echo "<td>" . $row["jam_selesai"] . "</td>";
                                echo "<td>" . $row["nama_dosen"] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>Tidak ada data KRS.</td></tr>";
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


<!-- Modal for adding KRS entries -->
<div class="modal fade" id="tambahKRSModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kartu Rencana Studi (KRS)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form to add a new KRS entry with dropdowns to select Mahasiswa and Matkul -->
                <form method="POST" action="include/proses_tambah_krs.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="mahasiswa">Nama Mahasiswa</label>
                        <select class="form-control" id="mahasiswa" name="mahasiswa" required>
                            <option value="" selected disabled>Silahkan pilih Mahasiswa</option>

                            <?php
                            include '../include/config.php';
                            // Query to retrieve Mahasiswa data
                            $mahasiswaQuery = "SELECT id, nama_mahasiswa FROM mahasiswa";

                            $mahasiswaResult = $conn->query($mahasiswaQuery);

                            if ($mahasiswaResult) {
                                while ($row = $mahasiswaResult->fetch_assoc()) {
                                    $idMahasiswa = $row["id"];
                                    $namaMahasiswa = $row["nama_mahasiswa"];
                                    echo "<option value='$idMahasiswa'>$namaMahasiswa</option>";
                                }
                            } else {
                                // Query execution failed
                                echo "Query execution failed: " . $conn->error;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="matkul">Mata Kuliah</label>
                        <select class="form-control" id="matkul" name="matkul" required>
                            <option value="" selected disabled>Silahkan pilih Mata Kuliah</option>

                            <?php
                            
                            // Query to retrieve Matkul data
                            $matkulQuery = "SELECT id, nama_matkul FROM matkul";

                            $matkulResult = $conn->query($matkulQuery);

                            if ($matkulResult) {
                                while ($row = $matkulResult->fetch_assoc()) {
                                    $idMatkul = $row["id"];
                                    $namaMatkul = $row["nama_matkul"];
                                    echo "<option value='$idMatkul'>$namaMatkul</option>";
                                }
                            } else {
                                // Query execution failed
                                echo "Query execution failed: " . $conn->error;
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>