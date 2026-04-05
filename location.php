<?php
session_start();
include 'config.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);


$selected_location = isset($_GET['location']) ? $_GET['location'] : '';


$sql = "SELECT id, product_code, name, status, image FROM products";
$params = [];


if (!empty($selected_location)) {
    $sql .= " WHERE location = ?";
}


$stmt = $conn->prepare($sql);


if (!empty($selected_location)) {
    $stmt->bindParam(1, $selected_location, PDO::PARAM_STR);
}


$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สินค้าตามสถานที่เก็บ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            color: black;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-secondary {
            background-color: #6a11cb;
            border: none;
            transition: 0.3s;
        }
        .btn-secondary:hover {
            background-color: #2575fc;
        }
        img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center"><i class="fa-solid fa-box"></i> สถานที่เก็บครุภัณฑ์</h2>
    

    <form method="GET" class="mb-4">
        <label class="form-label"><i class="fa-solid fa-map-marker-alt"></i> เลือกสถานที่:</label>
        <select name="location" class="form-select" onchange="this.form.submit()">
            <option value="">ทั้งหมด</option>
            <?php for ($i = 5001; $i <= 5009; $i++): ?>
                <option value="c<?= $i ?>" <?= $selected_location == "c$i" ? "selected" : "" ?>>C<?= $i ?></option>
            <?php endfor; ?>
        </select>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th><i class="fa-solid fa-barcode"></i> รหัสครุภัณฑ์</th>
                <th><i class="fa-solid fa-box"></i> ชื่อครุภัณฑ์</th>
                <th><i class="fa-solid fa-circle-info"></i> สถานะ</th>
                <th><i class="fa-solid fa-image"></i> รูปภาพ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): 
                $rowClass = ($row['status'] == 'เสียหาย') ? 'table-danger' : (($row['status'] == 'ใช้งานได้') ? 'table-success' : '');
                $image_src = !empty($row['image']) ? 'show_image.php?id=' . $row['id'] : 'no-image.png'; 
            ?>
            <tr class="text-center <?= $rowClass ?>">
                <td><?= htmlspecialchars($row['product_code']); ?></td>
                <td><?= htmlspecialchars($row['name']); ?></td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td class="text-center"><img src="<?= htmlspecialchars($row['image']); ?>" width="50" class="rounded"></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3"><i class="fa fa-arrow-left"></i> ย้อนกลับ</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
