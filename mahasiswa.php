<?php
// Include necessary configurations and header
require_once 'include/config.php';
include 'assets/navbar.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Scan QR Code</h1>
    <!-- Scan QR Code Section -->
    <div id="scanner-container" class="card shadow mb-4">
        <div class="card-body">
            <video id="scanner" width="100%"></video>
        </div>
    </div>
</div>

<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Check if the browser supports getUserMedia
        if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {
            // Access the device camera and start scanning
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function (stream) {
                    // Initialize the scanner
                    var scanner = new Instascan.Scanner({ video: document.getElementById('scanner') });

                    // Listen for QR code scans
                    scanner.addListener('scan', function (content) {
                        // Redirect to the attendance processing page with the scanned content
                        window.location.href = 'process_attendance.php?qr_content=' + encodeURIComponent(content);
                    });

                    // Start scanning
                    Instascan.Camera.getCameras().then(function (cameras) {
                        if (cameras.length > 0) {
                            scanner.start(cameras[0]);
                        } else {
                            console.error('No cameras found.');
                        }
                    });

                })
                .catch(function (error) {
                    console.error('Error accessing the camera:', error);
                });
        } else {
            console.error('getUserMedia is not supported.');
        }
    });
</script>

<?php
// Include footer
include 'assets/footer.php';
?>
