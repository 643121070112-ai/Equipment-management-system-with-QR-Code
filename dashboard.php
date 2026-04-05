<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT * FROM products";
if (!empty($search)) {
    $sql .= " WHERE name LIKE :search1 OR product_code LIKE :search2 OR location LIKE :search3";
}

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->execute([
        ':search1' => $search_param,
        ':search2' => $search_param,
        ':search3' => $search_param
    ]);
} else {
    $stmt->execute();
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$admin_log_query = "SELECT admin_name, login_time, logout_time FROM admin_logs ORDER BY login_time DESC LIMIT 3";
$admin_stmt = $conn->prepare($admin_log_query);
$admin_stmt->execute();
$admin_logs = $admin_stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แผงควบคุมแอดมิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>

        body {
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        }

        .navbar {
            background: linear-gradient(45deg, #007bff, #6610f2);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .btn {
            border-radius: 25px;
        }
        .btn-success, .btn-info, .btn-primary {
            transition: 0.3s;
        }
        .btn-success:hover {
            background-color: #28a745;
            transform: scale(1.05);
        }
        .btn-info:hover {
            background-color: #17a2b8;
            transform: scale(1.05);
        }
        .btn-primary:hover {
            background-color: #007bff;
            transform: scale(1.05);
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-hover tbody tr:hover {
            background-color: #e3f2fd;
        }
        .status-icon {
            font-size: 18px;
            margin-right: 5px;
        }
        .rounded-img {
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }
        .search-box {
            border-radius: 50px;
            padding: 10px 20px;
            border: 2px solid #007bff;
            width: 100%;
        }
    </style>
</head>
<body>


<nav class="navbar navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fa-solid fa-cog"></i> แผงควบคุมแอดมิน</a>
        <a class="btn btn-danger" href="logout.php"><i class="fa-solid fa-sign-out-alt"></i> ออกจากระบบ</a>
    </div>
</nav>

<div class="container-fluid mt-4">
    <h2 class="text-center mb-4">👋 ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['admin']); ?></strong></h2>
    <div class="row">

        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">🔑 แอดมินที่เข้าใช้งานล่าสุด</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($admin_logs as $log): ?>
                        <?php 
                            $statusIcon = is_null($log['logout_time']) 
                                ? '<i class="fa-solid fa-circle text-success"></i>'  
                                : '<i class="fa-solid fa-circle text-danger"></i>'; 
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= $statusIcon ?> <?= htmlspecialchars($log['admin_name']); ?></span>
                            <small class="text-muted"><?= htmlspecialchars($log['login_time']); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>


        <div class="col-md-9">
            <div class="d-flex gap-2 mb-3">
                <a href="add_product.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> เพิ่มครุภัณฑ์</a>
                <a href="stats.php" class="btn btn-info"><i class="fa-solid fa-chart-bar"></i> ดูสถิติ</a>
                <a href="location.php" class="btn btn-primary"><i class="fa-solid fa-map-marker-alt"></i> ดูตามสถานที่</a>
                <a href="qr_generator.php" class="btn btn-dark"><i class="fa-solid fa-qrcode"></i> ดาวน์โหลด QR Code</a>
            </div>


            <form method="GET" action="dashboard.php" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="🔍 ค้นหาสินค้า..." value="<?= htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
                </div>
            </form>


            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>รูป</th>
                        <th>ชื่อครุภัณฑ์</th>
                        <th>รหัส</th>
                        <th>สถานที่</th>
                        <th>สถานะ</th>
                        <th>QR</th>
                        <th>อัปเดต</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $row): ?>
                    <tr class="<?= ($row['status'] == 'เสียหาย') ? 'table-danger' : (($row['status'] == 'ใช้งานได้') ? 'table-success' : '') ?>">
                        <td class="text-center"><img src="<?= htmlspecialchars($row['image']); ?>" width="50" class="rounded"></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['product_code']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['location']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['status']); ?></td>
                        <td class="text-center"><img src="<?= htmlspecialchars($row['qr_code']); ?>" width="50" class="rounded"></td>
                        <td class="text-center"><?= htmlspecialchars($row['updated_at']); ?></td>
                        <td class="text-center">
                            <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-edit"></i></a>
                            <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?');"><i class="fa-solid fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>