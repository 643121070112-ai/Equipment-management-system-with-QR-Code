<?php
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/qrkaru/';

$db_host = 'localhost';  
$db_user = 'qrkaru';
$db_pass = '!Qazxsw2';
$db_name = 'qrkaru';  
$db_port = 3306; 

try {
    $conn = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
            PDO::ATTR_EMULATE_PREPARES => false 
        ]
    );
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage()); 
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


