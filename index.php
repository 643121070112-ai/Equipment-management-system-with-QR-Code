<?php
session_start();
include 'config.php'; 

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {

    $sql = "SELECT * FROM products WHERE name LIKE :search1 OR product_code LIKE :search2 OR location LIKE :search3";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$search%";
    $stmt->bindValue(':search1', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':search2', $searchTerm, PDO::PARAM_STR);
    $stmt->bindValue(':search3', $searchTerm, PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM products";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📦 รายการสินค้า</title>

    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .navbar {
            background: #2c3e50;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            width: 100%;
        }
        .navbar a {
            color: white;
            font-weight: 500;
            margin: 0 10px;
            text-decoration: none;
            transition: 0.3s;
        }
        .navbar a:hover {
            color: #f39c12;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            margin-top: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 1000px;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.8);
        }
        .table th {
            background-color: #34495e;
            color: white;
            font-size: 16px;
            text-align: center;
        }
        .table tbody tr:hover {
            background-color: rgba(241, 241, 241, 0.8);
        }
        .status-available {
            color: green;
            font-weight: bold;
        }
        .status-damaged {
            color: red;
            font-weight: bold;
        }
        .search-box {
            max-width: 400px;
            margin: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a href="index.php" class="navbar-brand"><i class="fa-solid fa-box"></i> ระบบคลังครุภัณฑ์</a>
        <div class="ms-auto">
            <a href="index.php"><i class="fa-solid fa-home"></i> หน้าแรก</a>
            <a href="login.php"><i class="fa-solid fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4"><i class="fa-solid fa-clipboard-list"></i> รายการครุภัณฑ์</h2>

    <form method="GET" action="index.php" class="search-box mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="🔍 ค้นหาสินค้า..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
        </div>
    </form>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ชื่อครุภัณฑ์</th>
                <th>รหัสครุภัณฑ์</th>
                <th>สถานที่เก็บ</th>
                <th>รูปภาพ</th>
                <th>วันที่อัปเดต</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): 
                $statusClass = ($row['status'] == 'ใช้งานได้') ? 'status-available' : 'status-damaged';
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['product_code']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><img src="<?= htmlspecialchars($row['image']) ?>" width="50" class="rounded"></td>
                    <td><?= htmlspecialchars($row['updated_at']) ?></td>
                    <td class="<?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
