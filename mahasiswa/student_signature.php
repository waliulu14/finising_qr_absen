<?php
session_start();

// Include necessary configurations
require_once '../include/config.php';
include 'assets/navbar.php';

// Check if the user is not logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header('Location: ../login.php');
    exit(); // Stop script execution
}

// Get matkul_id and id_mahasiswa from the URL
if (isset($_GET['matkul_id']) && isset($_GET['id_mahasiswa'])) {
    $matkul_id = $_GET['matkul_id'];
    $id_mahasiswa = $_GET['id_mahasiswa'];

    // Display the signature page content
    echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Digital Signature Page</title>
            <style>
                #signature-pad {
                    border: 1px solid #ccc;
                    border-radius: 0.5rem;
                    width: 100%;
                    height: 400px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1 class='alert alert-success mt-5 text-dark'>
                    Digital Signature Page
                </h1>
                <div class='card'>
                    <div class='card-header'>
                        Digital Signature Form
                    </div>
                    <div class='card-body'>
                        <canvas id='signature-pad' class='signature-pad'></canvas>
                        <div style='float: left;'>
                            <button id='clear-signature' class='btn btn-danger'>
                                Clear Signature
                            </button>
                        </div>
                        <div style='float: right;'>
                            <form id='signature-form' method='post' action='process_student_signature.php'>
                                <input type='hidden' name='matkul_id' value='$matkul_id'>
                                <input type='hidden' name='id_mahasiswa' value='$id_mahasiswa'>
                                <input type='hidden' id='signature-input' name='signature'>
                                <button type='submit' class='btn btn-primary'>
                                    Submit Signature
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    resizeCanvas();
                });

                function resizeCanvas() {
                    var ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext('2d').scale(ratio, ratio);
                }

                var canvas = document.getElementById('signature-pad');
                var signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)'
                });

                document.getElementById('clear-signature').addEventListener('click', function () {
                    signaturePad.clear();
                });

                document.getElementById('signature-form').addEventListener('submit', function (e) {
                    e.preventDefault();
                    var signatureInput = document.getElementById('signature-input');
                    signatureInput.value = signaturePad.toDataURL();
                    this.submit();
                });
            </script>
        </body>
        </html>";

    include 'assets/footer.php'; // Include footer.php
} else {
    echo "Invalid matkul_id or id_mahasiswa parameter.";
}
?>
