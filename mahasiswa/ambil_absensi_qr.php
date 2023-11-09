<?php
require_once '../include/config.php'; // Sesuaikan dengan path ke berkas config Anda
include 'assets/navbar.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ambil Absensi dengan QR Code</title>
    <!-- Include library JavaScript untuk mengakses kamera -->
    <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
    <style>
        video {
            width: 100%;
            max-width: 500px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h1>Ambil Absensi dengan QR Code</h1>
    <video id="qr-video" playsinline></video>
    <script src="https://rawgit.com/sitepoint-editors/jsqrcode/master/src/qr_packed.js"></script>
    <script>
        // Mengakses kamera pengguna
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (stream) {
                // Menghubungkan video dari kamera ke elemen video
                var video = document.getElementById('qr-video');
                video.srcObject = stream;
                video.play();
                
                // Mendeteksi QR code dari video kamera
                const qrCanvas = document.createElement('canvas');
                const qrContext = qrCanvas.getContext('2d');
                
                var qrWorker = new Worker("js/qrworker.js");

                qrWorker.postMessage({ type: "cmd", data: "stop" });
                
                qrWorker.onmessage = function (event) {
                    const decoded = event.data;
                    if (decoded) {
                        // QR code berhasil terdeteksi
                        alert("QR Code berhasil terdeteksi: " + decoded);
                        
                        // Mengirim data absensi ke server (AJAX)
                        var absensiData = {
                            matkul_id: decoded
                        };
                        
                        // Mengirim data absensi ke server (menggunakan AJAX)
                        fetch('proses_absensi.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(absensiData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                        })
                        .catch(error => {
                            console.error('Gagal mengirim data absensi:', error);
                        });
                    }
                };
                
                // Memindai frame video untuk QR code
                function scanFrame() {
                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                        qrCanvas.height = video.videoHeight;
                        qrCanvas.width = video.videoWidth;
                        qrContext.drawImage(video, 0, 0, qrCanvas.width, qrCanvas.height);
                        try {
                            qrWorker.postMessage({
                                type: "cmd",
                                data: qrCanvas
                            });
                        } catch (e) {
                            // QR code tidak ditemukan pada frame
                        }
                    }
                    requestAnimationFrame(scanFrame);
                }
                scanFrame();
            })
            .catch(function (error) {
                console.error('Gagal mengakses kamera:', error);
            });
    </script>
</body>
</html>
