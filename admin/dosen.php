<?php include 'assets/navbar.php'; ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Daftar Dosen</h1>
    <!-- DataTales Example -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahDosenModal">
        Tambah Dosen
    </button>
    <br>
    <br>
    <?php if (isset($_GET['success']) && $_GET['success'] == 'hapus') : ?>
        <div class="alert alert-success mt-3" role="alert">
            Dosen berhasil dihapus.
        </div>
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 'hapus') : ?>
        <div class="alert alert-danger mt-3" role="alert">
            Terjadi kesalahan saat menghapus dosen.
        </div>
    <?php endif; ?>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Dosen</th>
                            <th>NID</th>
                            <th>Email</th>
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

                        // Query to retrieve dosen data
                        $query = "SELECT D.id, D.nama_dosen, D.nid, D.email
                        FROM dosen D
                        INNER JOIN user U ON D.id_user = U.id
                        WHERE U.level = 'dosen'";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                $id = $row["id"];
                                $nama_dosen = $row["nama_dosen"];
                                $nid = $row["nid"];
                                $email = $row["email"];

                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $nama_dosen . "</td>";
                                echo "<td>" . $nid . "</td>";
                                echo "<td>" . $email . "</td>";
                                echo "<td>
                                    <a href='#' class='btn btn-info btn-sm' data-toggle='modal' data-target='#editDosenModal$id'>Edit</a>
                                </td>";
                                echo "</tr>";

                                // Buat modal konfirmasi penghapusan untuk setiap baris
                                echo "<div class='modal fade' id='hapusDosenModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='exampleModalLabel'>Konfirmasi Hapus Dosen</h5>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                Apakah Anda yakin ingin menghapus dosen ini?
                                            </div>
                                            <div class='modal-footer'>
                                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Batal</button>
                                                <a href='include/proses_hapus_dosen.php?id=$id' class='btn btn-danger'>Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>";

                                // Buat modal edit untuk setiap baris
                                echo "<div class='modal fade' id='editDosenModal$id' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='exampleModalLabel'>Edit Dosen</h5>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                <!-- Form untuk mengedit dosen -->
                                                <form method='POST' action='include/proses_edit_dosen.php'>
                                                    <input type='hidden' name='id' value='$id'>
                                                    <div class='form-group'>
                                                        <label for='nama_dosen'>Nama Dosen</label>
                                                        <input type='text' class='form-control' id='nama_dosen' name='nama_dosen' value='$nama_dosen' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='nid'>NID</label>
                                                        <input type='text' class='form-control' id='nid' name='nid' value='$nid' required>
                                                    </div>
                                                    <div class='form-group'>
                                                        <label for='email'>Email</label>
                                                        <input type='email' class='form-control' id='email' name='email' value='$email' required>
                                                    </div>
                                                    <button type='submit' class='btn btn-primary'>Simpan</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tidak ada data dosen.</td></tr>";
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

<!-- Modal Tambah Dosen -->
<div class="modal fade" id="tambahDosenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Dosen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form untuk menambahkan dosen -->
                <form method='POST' action='include/proses_tambah_dosen.php'>
                    <input type='hidden' name='level' value='dosen'>
                    <div class='form-group'>
                        <label for='nama_dosen'>Nama Dosen</label>
                        <input type='text' class='form-control' id='nama_dosen' name='nama_dosen' required>
                    </div>
                    <div class='form-group'>
                        <label for='nid'>NID</label>
                        <input type='text' class='form-control' id='nid' name='nid' required>
                    </div>
                    <div class='form-group'>
                        <label for='email'>Email</label>
                        <input type='email' class='form-control' id='email' name='email' required>
                    </div>
                    <div class='form-group'>
                        <label for='username'>Username</label>
                        <input type='text' class='form-control' id='username' name='username' required>
                    </div>
                    <div class='form-group'>
                        <label for='password'>Password</label>
                        <input type='password' class='form-control' id='password' name='password' required>
                    </div>
                    <button type='submit' class='btn btn-primary'>Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'assets/footer.php'; ?>
