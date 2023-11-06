<?php include 'assets/navbar.php'; ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Mata Kuliah</h1>
    <!-- DataTales Example -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahMatkulModal">
        Tambah Mata Kuliah
    </button>
    <br>
    <br>
    <?php if (isset($_GET['success']) && $_GET['success'] == 'hapus') : ?>
        <div class="alert alert-success mt-3" role="alert">
            Mata Kuliah berhasil dihapus.
        </div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 'hapus') : ?>
        <div class="alert alert-danger mt-3" role="alert">
            Terjadi kesalahan saat menghapus Mata Kuliah.
        </div>
    <?php endif; ?>
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
                            <th>Dosen Pengampu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../include/config.php';

                        // Check if the connection is successful
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Query to retrieve course data along with the associated professor's name
                        $query = "SELECT M.id, M.kode_matkul, M.nama_matkul, M.kelas, M.hari, M.jam_mulai, M.jam_selesai, D.nama_dosen
                                  FROM matkul M
                                  INNER JOIN dosen D ON M.id_dosen = D.id";
                       
                       $result = $conn->query($query);
                       if ($result->num_rows > 0) {
                           $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                $id = $row["id"];
                                $kode_matkul = $row["kode_matkul"];
                                $nama_matkul = $row["nama_matkul"];
                                $kelas = $row["kelas"];
                                $hari = $row["hari"];
                                $jam_mulai = $row["jam_mulai"];
                                $jam_selesai = $row["jam_selesai"];
                                $dosen_pengampu = $row["nama_dosen"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $kode_matkul . "</td>";
                                echo "<td>" . $nama_matkul . "</td>";
                                echo "<td>" . $kelas . "</td>";
                                echo "<td>" . $hari . "</td>";
                                echo "<td>" . $jam_mulai . "</td>";
                                echo "<td>" . $jam_selesai . "</td>";
                                echo "<td>" . $dosen_pengampu . "</td>";
                                echo "<td>
                                    <a href='#' class='btn btn-info btn-sm' data-toggle='modal' data-target='#editMatkulModal$id'>Edit</a>
                                    <a href='#' class='btn btn btn-danger btn-sm' data-toggle='modal' data-target='#hapusMatkulModal$id'>Hapus</a>

                                </td>";
                                echo "</tr>";

                                // Create a modal for confirming course deletion
                                echo "<div class='modal fade' id='hapusMatkulModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='exampleModalLabel'>Konfirmasi Hapus Mata Kuliah</h5>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                Apakah Anda yakin ingin menghapus mata kuliah ini?
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Batal</button>
                                                <a href='include/proses_hapus_matkul.php?id=$id' class='btn btn-danger'>Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>";

                                // Update the modal ID to 'editMatkulModal$id'
                                echo "<div class='modal fade' id='editMatkulModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                <div class='modal-dialog' role='document'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='exampleModalLabel'>Edit Mata Kuliah</h5>
                                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                <span aria-hidden='true'>&times;</span>
                                            </button>
                                        </div>
                                        <div class='modal-body'>
                                            <!-- Form for editing course information -->
                                            <form method='POST' action='include/proses_edit_matkul.php' enctype='multipart/form-data'>
                                                <input type='hidden' name='id' value='$id'>
                                                <div class='form-group'>
                                                    <label for='kode_matkul'>Kode Matkul</label>
                                                    <input type='text' class='form-control' id='kode_matkul' name='kode_matkul' value='$kode_matkul' required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='nama_matkul'>Nama Matkul</label>
                                                    <input type='text' class='form-control' id='nama_matkul' name='nama_matkul' value='$nama_matkul' required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='kelas'>Kelas</label>
                                                    <input type='text' class='form-control' id='kelas' name='kelas' value='$kelas' required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='hari'>Hari</label>
                                                    <input type='text' class='form-control' id='hari' name='hari' value='$hari' required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='jam_mulai'>Jam Mulai</label>
                                                    <input type='text' class='form-control' id='jam_mulai' name='jam_mulai' value='$jam_mulai' required>
                                                </div>
                                                <div class='form-group'>
                                                    <label for='jam_selesai'>Jam Selesai</label>
                                                    <input type='text' class='form-control' id='jam_selesai' name='jam_selesai' value='$jam_selesai' required>
                                                </div>
                                            
                                                <!-- Add other course-related form fields here -->
                                                <button type='submit' class='btn btn-primary'>Simpan</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </div>";
                            }
                        } else {
                            echo "<tr><td colspan='9'>Tidak ada data mata kuliah.</td></tr>";
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



<div class="modal fade" id="tambahMatkulModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Mata Kuliah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form to add a new course with a dropdown to select a Dosen -->
                <form method="POST" action="include/proses_tambah_matkul.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="dosen">Dosen</label>
                        <select class="form-control" id="dosen" name="dosen" required>
                            <option value="" selected disabled>Silahkan pilih Dosen Pengejar</option>

                            <?php
                            include '../include/config.php';
                            // Query to retrieve Dosen data
                            $dosenQuery = "SELECT id, nama_dosen FROM dosen";

                            $dosenResult = $conn->query($dosenQuery);

                            if ($dosenResult) {
                                while ($row = $dosenResult->fetch_assoc()) {
                                    $idDosen = $row["id"];
                                    $namaDosen = $row["nama_dosen"];
                                    echo "<option value='$idDosen'>$namaDosen</option>";
                                }
                            } else {
                                // Query execution failed
                                echo "Query execution failed: " . $conn->error;
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kode_matkul">Kode Mata Kuliah</label>
                        <input type="text" class="form-control" id="kode_matkul" name="kode_matkul" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_matkul">Nama Mata Kuliah</label>
                        <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" required>
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <input type="text" class="form-control" id="kelas" name="kelas" required>
                    </div>
                    <div class="form-group">
                        <label for="hari">Hari</label>
                        <input type="text" class="form-control" id="hari" name="hari" required>
                    </div>
                    <div class="form-group">
                        <label for="jam_mulai">Jam Mulai</label>
                        <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                    </div>
                    <div class="form-group">
                        <label for="jam_selesai">Jam Selesai</label>
                        <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>