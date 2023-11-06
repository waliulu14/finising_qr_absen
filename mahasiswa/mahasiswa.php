<?php include 'assets/navbar.php'; ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Mahasiswa</h1>
    <!-- DataTales Example -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahMahasiswaModal">
        Tambah Mahasiswa
    </button>
    <br>
    <br>
    <?php if (isset($_GET['success']) && $_GET['success'] == 'hapus') : ?>
        <div class="alert alert-success mt-3" role="alert">
            Mahasiswa berhasil dihapus.
        </div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 'hapus') : ?>
        <div class="alert alert-danger mt-3" role="alert">
            Terjadi kesalahan saat menghapus mahasiswa.
        </div>
    <?php endif; ?>
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

                        // Query to retrieve mahasiswa data
                        $query = "SELECT M.id, M.nama_mahasiswa, M.nim, M.email, M.milis
                            FROM mahasiswa M
                            INNER JOIN user U ON M.id_user = U.id
                            WHERE U.level = 'mahasiswa'";
                        
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                $id = $row["id"];
                                $nama_mahasiswa = $row["nama_mahasiswa"];
                                $nim = $row["nim"];
                                $email = $row["email"];
                                $milis = $row["milis"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $nama_mahasiswa . "</td>";
                                echo "<td>" . $nim . "</td>";
                                echo "<td>" . $email . "</td>";
                                echo "<td>" . $milis . "</td>";
                                echo "<td>
                                    <a href='#' class='btn btn-info btn-sm' data-toggle='modal' data-target='#editMahasiswaModal$id'>Edit</a>
                                </td>";
                                echo "</tr>";

                                // Buat modal konfirmasi penghapusan untuk setiap baris
                                echo "<div class='modal fade' id='hapusMahasiswaModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='exampleModalLabel'>Konfirmasi Hapus Mahasiswa</h5>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                Apakah Anda yakin ingin menghapus mahasiswa ini?
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Batal</button>
                                                <a href='include/proses_hapus_mahasiswa.php?id=$id' class='btn btn-danger'>Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>";

                                // Buat modal edit untuk setiap baris
                                echo "<div class='modal fade' id='editMahasiswaModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='exampleModalLabel'>Edit Mahasiswa</h5>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                <!-- Form untuk mengedit mahasiswa -->
                                                <form method='POST' action='include/proses_edit_mahasiswa.php' enctype='multipart/form-data'>
                                                    <input type='hidden' name='id' value='$id'>
                                                    <div class='form-group'>
                                                        <label for='nama_mahasiswa'>Nama Mahasiswa</label>
                                                        <input type='text' class='form-control' id='nama_mahasiswa' name='nama_mahasiswa' value='$nama_mahasiswa' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for 'nim'>NIM</label>
                                                        <input type='text' class='form-control' id='nim' name='nim' value='$nim' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='email'>Email</label>
                                                        <input type='email' class='form-control' id='email' name='email' value='$email' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='milis'>Milis</label>
                                                        <input type='text' class='form-control' id='milis' name='milis' value='$milis' required>
                                                    </div>
                                                    <button type='submit' class='btn btn-primary'>Simpan</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak ada data mahasiswa.</td></tr>";
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

<!-- Modal Tambah Mahasiswa -->
<div class="modal fade" id="tambahMahasiswaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='exampleModalLabel'>Tambah Mahasiswa</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body'>
                <!-- Form untuk menambahkan akun pengguna -->
                <form method='POST' action='include/proses_tambah_mahasiswa.php'>
                    <div class='form-group'>
                        <label for='username'>Username</label>
                        <input type='text' class='form-control' id='username' name='username' required>
                    </div>
                    <div class='form-group'>
                        <label for='password'>Password</label>
                        <input type='password' class='form-control' id='password' name='password' required>
                    </div>
                    <input type='hidden' name='level' value='mahasiswa'>
                    <!-- Form untuk menambahkan mahasiswa -->
                    <div class='form-group'>
                        <label for='nama_mahasiswa'>Nama Mahasiswa</label>
                        <input type='text' class='form-control' id='nama_mahasiswa' name='nama_mahasiswa' required>
                    </div>
                    <div class='form-group'>
                        <label for 'nim'>NIM</label>
                        <input type='text' class='form-control' id='nim' name='nim' required>
                    </div>
                    <div class='form-group'>
                        <label for='email'>Email</label>
                        <input type='email' class='form-control' id='email' name='email' required>
                    </div>
                    <div class='form-group'>
                        <label for='milis'>Milis</label>
                        <select class='form-control' id='milis' name='milis' required>
                            <option value='' selected disabled>Silahkan pilih milis</option>
                            <option value='2017'>2017</option>
                            <option value='2018'>2018</option>
                            <option value='2019'>2019</option>
                            <option value='2020'>2020</option>
                            <option value='2021'>2021</option>
                            <!-- Tambahkan pilihan milis lainnya sesuai kebutuhan -->
                        </select>
                    </div>
                    <button type='submit' class='btn btn-primary'>Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'assets/footer.php'; ?>
