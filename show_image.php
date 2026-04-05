<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $query = "SELECT image FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $image);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($image) {
        header("Content-Type: image/jpeg"); 
        echo $image;
    } else {
        header("Content-Type: image/png");
        readfile("no-image.png");
    }
}
?>
