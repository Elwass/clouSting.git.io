<?php
require_once __DIR__ . '/../vendor/midtrans/Midtrans.php';

use Midtrans\Config as MidtransConfig;

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cloudhost_db';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

$projectUploadDir = __DIR__ . '/../public/uploads/projects';
if (!is_dir($projectUploadDir)) {
    mkdir($projectUploadDir, 0775, true);
}

define('PROJECT_UPLOAD_DIR', $projectUploadDir);
define('PROJECT_UPLOAD_URI', '/uploads/projects');

$midtransServerKey = getenv('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-yourkey';
$midtransClientKey = getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-yourkey';
$midtransIsProduction = getenv('MIDTRANS_IS_PRODUCTION') ? filter_var(getenv('MIDTRANS_IS_PRODUCTION'), FILTER_VALIDATE_BOOLEAN) : false;

define('MIDTRANS_SERVER_KEY', $midtransServerKey);
define('MIDTRANS_CLIENT_KEY', $midtransClientKey);
define('MIDTRANS_IS_PRODUCTION', $midtransIsProduction);

MidtransConfig::$serverKey = MIDTRANS_SERVER_KEY;
MidtransConfig::$clientKey = MIDTRANS_CLIENT_KEY;
MidtransConfig::$isProduction = MIDTRANS_IS_PRODUCTION;
