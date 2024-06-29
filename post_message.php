<?php

include 'db_connect.php';

$name = $_POST['name'];
$avatar = $_POST['avatar'];
$message = $_POST['message'];
$mood = $_POST['mood'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_code = hash('sha256', $ip_address);

// 使用预处理语句防止SQL注入
$stmt = $conn->prepare("INSERT INTO messages (name, avatar, message, mood, user_code, likes) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->bind_param("sssss", $name, $avatar, $message, $mood, $user_code);

if ($stmt->execute()) {
    echo "新记录插入成功";
} else {
    echo "错误: " . $stmt->error;
}

$stmt->close();
$conn->close();
