<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ไม่พบสินค้าที่ต้องการแก้ไข");
}

$product_id = intval($_GET['id']);


$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("ไม่พบสินค้านี้");
}

$upload_dir = "uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $location = htmlspecialchars($_POST['location']);
    $status = htmlspecialchars($_POST['status']);
    $imageName = $product['image']; 


    if (!empty($_FILES["product_image"]["name"])) {
        $allowed_ext = ['jpg', 'jpeg', 'png'];
        $image_ext = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));

        if (!in_array($image_ext, $allowed_ext)) {
            echo "<script>alert('❌ อัปโหลดได้เฉพาะไฟล์ JPG, JPEG, PNG เท่านั้น');</script>";
            exit();
        }

        $new_image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
        $target_file = $upload_dir . $new_image_name;


        if (!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
            unlink($upload_dir . $product['image']);
        }


        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $imageName = $upload_dir . $new_image_name; 
        } else {
            echo "<script>alert('❌ ไม่สามารถอัปโหลดรูปภาพได้');</script>";
            exit();
        }
    }


    $update_sql = "UPDATE products SET name=?, location=?, image=?, status=?, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->execute([$name, $location, $imageName, $status, $product_id]);

    echo "<script>alert('✅ อัปเดตข้อมูลสำเร็จ!'); window.location='dashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลครุภัณฑ์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ffecd2, #fcb69f);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: white;
        }
        h2 {
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            text-align: center;
            color: #444;
        }
        .form-label {
            font-weight: bold;
            color: #555;
        }
        .btn-primary {
            background: #ff6b6b;
            border: none;
        }
        .btn-primary:hover {
            background: #ff3b3b;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .image-preview {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .image-preview img {
            max-width: 150px;
            border-radius: 10px;
            border: 2px solid #ddd;
        }
    </style>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const imgElement = document.getElementById("previewImage");
                imgElement.src = reader.result;
                imgElement.style.display = "block";
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</head>
<body>
    <div class="card">
        <h2><i class="fa-solid fa-edit"></i> แก้ไขข้อมูลครุภัณฑ์</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">ชื่อครุภัณฑ์</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">สถานที่เก็บ</label>
                <select name="location" class="form-select" required>
                    <?php
                    $locations = ['c5001', 'c5002', 'c5003', 'c5004', 'c5005', 'c5006', 'c5007', 'c5008', 'c5009'];
                    foreach ($locations as $loc) {
                        $selected = ($loc == $product['location']) ? "selected" : "";
                        echo "<option value='$loc' $selected>$loc</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">สถานะ</label>
                <select name="status" class="form-select" required>
                    <option value="ใช้งานได้" <?= ($product['status'] == 'ใช้งานได้') ? "selected" : ""; ?>>ใช้งานได้</option>
                    <option value="เสียหาย" <?= ($product['status'] == 'เสียหาย') ? "selected" : ""; ?>>เสียหาย</option>
                    <option value="ไม่มีการใช้งาน" <?= ($product['status'] == 'ไม่มีการใช้งาน') ? "selected" : ""; ?>>ไม่มีการใช้งาน</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">รูปภาพ (อัปโหลดใหม่หากต้องการเปลี่ยน)</label>
                <input type="file" name="product_image" class="form-control" onchange="previewImage(event)">
                <div class="image-preview text-center">
    <svg id="previewIcon" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="text-muted" viewBox="0 0 24 24">
        <path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5zm14 10-2.5-3-3 4L8 13l-3 4h14zm-8-7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
    </svg>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewIcon = document.getElementById("previewIcon");
                previewIcon.outerHTML = `<img id="previewImage" src="${e.target.result}" width="100" class="rounded">`;
            };
            reader.readAsDataURL(file);
        }
    }
</script>

            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-save"></i> บันทึก</button>
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">ยกเลิก</a>
        </form>
    </div>
</body>
</html>
