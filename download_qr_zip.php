<?php
session_start();
include 'config.php';


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$zip_file = "qr_codes.zip";
$qr_folder = "qrcodes/";


$files = glob($qr_folder . "*.png");
if (empty($files)) {
    die("❌ ไม่มีไฟล์ QR Code ให้ดาวน์โหลด");
}


$zip = new ZipArchive;
if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();


    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="qr_codes.zip"');
    header('Content-Length: ' . filesize($zip_file));
    readfile($zip_file);


    unlink($zip_file);
} else {
    die("❌ ไม่สามารถสร้างไฟล์ ZIP ได้");
}
?>

