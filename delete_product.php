<?php
session_start();
include 'config.php';


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];


    $query = "SELECT image FROM products WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row) {

        if (!empty($row['image'])) {
            $image_path = __DIR__ . '/' . ltrim($row['image'], '/'); 
            if (file_exists($image_path) && strpos(realpath($image_path), realpath(__DIR__)) === 0) {
                unlink($image_path);
            }
        }


        $delete_query = "DELETE FROM products WHERE id = :id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':id', $product_id, PDO::PARAM_INT);

        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "ลบสินค้าสำเร็จ!";
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบสินค้า";
        }
    } else {
        $_SESSION['error_message'] = "ไม่พบสินค้าที่ต้องการลบ";
    }
} else {
    $_SESSION['error_message'] = "ไม่มีข้อมูลสินค้า";
}


header("Location: dashboard.php");
exit();
?>

