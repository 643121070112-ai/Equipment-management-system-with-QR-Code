<?php
session_start();
include 'config.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}


$qr_dir = "qrcodes/";
if (!is_dir($qr_dir)) {
    mkdir($qr_dir, 0777, true);
}


$stmt = $conn->prepare("SELECT id, name, product_code, qr_code FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลัง QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #84fab0, #8fd3f4);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        h2 {
            font-weight: bold;
            color: #2c3e50;
        }
        .qr-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }
        .qr-card:hover {
            transform: scale(1.05);
        }
        .qr-card img {
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            width: 150px;
        }
        .btn {
            border-radius: 25px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .no-qr {
            color: red;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h2>📌 คลัง QR Code ครุภัณฑ์ทั้งหมด</h2>
    <a href="download_qr_zip.php" class="btn btn-success mb-4">📥 ดาวน์โหลด QR Code ทั้งหมด (ZIP)</a>

    <?php if (count($products) > 0) { ?>
        <div class="row">
            <?php foreach ($products as $row): 
                $qr_code = !empty($row['qr_code']) ? $row['qr_code'] : "qrcodes/no-qr.png"; 
                $qr_path = htmlspecialchars($qr_dir . basename($qr_code)); 
            ?>
                <div class="col-md-3 text-center mb-4">
                    <div class="qr-card">
                        <img src="<?= $qr_path; ?>" alt="QR Code">
                        <p class="fw-bold mt-2"><?= htmlspecialchars($row['name']); ?></p>
                        <p>รหัสครุภัณฑ์: <?= htmlspecialchars($row['product_code']); ?></p>
                        <?php if (!empty($row['qr_code'])) { ?>
                            <a href="<?= $qr_path; ?>" download class="btn btn-sm btn-primary">ดาวน์โหลด</a>
                        <?php } else { ?>
                            <p class="no-qr">❌ ไม่มี QR Code</p>
                        <?php } ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php } else { ?>
        <p class="text-danger">❌ ยังไม่มี QR Code ในระบบ</p>
    <?php } ?>

    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-secondary">กลับไปหน้าหลัก</a>
    </div>
</div>

</body>
</html>
