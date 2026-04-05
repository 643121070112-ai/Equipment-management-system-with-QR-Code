<?php
session_start();
include 'config.php';


if (!$conn) {
    die("Database connection failed!");
}


$total_sql = "SELECT COUNT(*) AS total FROM products";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute();
$total_result = $total_stmt->fetch(PDO::FETCH_ASSOC);
$total_products = $total_result['total'] ?? 0;


$active_sql = "SELECT COUNT(*) AS active FROM products WHERE status = 'ใช้งานได้'";
$active_stmt = $conn->prepare($active_sql);
$active_stmt->execute();
$active_result = $active_stmt->fetch(PDO::FETCH_ASSOC);
$active_products = $active_result['active'] ?? 0;


$damaged_sql = "SELECT COUNT(*) AS damaged FROM products WHERE status = 'เสียหาย'";
$damaged_stmt = $conn->prepare($damaged_sql);
$damaged_stmt->execute();
$damaged_result = $damaged_stmt->fetch(PDO::FETCH_ASSOC);
$damaged_products = $damaged_result['damaged'] ?? 0;


$inactive_sql = "SELECT COUNT(*) AS inactive FROM products WHERE status = 'ไม่มีการใช้งาน'";
$inactive_stmt = $conn->prepare($inactive_sql);
$inactive_stmt->execute();
$inactive_result = $inactive_stmt->fetch(PDO::FETCH_ASSOC);
$inactive_products = $inactive_result['inactive'] ?? 0;
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .card {
            cursor: pointer;
            border: none;
            transition: transform 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.2) !important;
        }

        .card h4, .card h2 {
            font-weight: bold;
        }

        #chartContainer {
            width: 100%;
            height: 500px;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #182848;
            border: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-secondary:hover {
            background-color: #4b6cb7;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">📊 สถิติครุภัณฑ์</h2>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary text-center p-3">
                    <h4>📦 ครุภัณฑ์ทั้งหมด</h4>
                    <h2><?php echo $total_products; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success text-center p-3">
                    <h4>✅ ใช้งานได้</h4>
                    <h2><?php echo $active_products; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger text-center p-3">
                    <h4>❌ เสียหาย</h4>
                    <h2><?php echo $damaged_products; ?></h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning text-center p-3">
                    <h4>⏳ ไม่มีการใช้งาน</h4>
                    <h2><?php echo $inactive_products; ?></h2>
                </div>
            </div>
        </div>

        <div class="mt-5" id="chartContainer">
            <canvas id="productChart"></canvas>
        </div>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">กลับไปที่ แผงควบคุมแอดมิน</a>
        </div>
    </div>

    <script>
        var ctx = document.getElementById('productChart').getContext('2d');
        var productChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['สินค้าทั้งหมด', 'ใช้งานได้', 'เสียหาย', 'ไม่มีการใช้งาน'],
                datasets: [{
                    label: 'จำนวนสินค้า',
                    data: [<?= $total_products ?>, <?= $active_products ?>, <?= $damaged_products ?>, <?= $inactive_products ?>],
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)'
                    ],
                    borderColor: [
                        'rgba(13, 110, 253, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 193, 7, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: { color: "#fff" }
                    },
                    y: {
                        ticks: { color: "#fff" }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: "#fff" }
                    }
                }
            }
        });
    </script>

</body>
</html>
