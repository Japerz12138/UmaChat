<?php

include 'db_connect.php';

$name = $_POST['name'];
$avatar = $_POST['avatar'];
$message = $_POST['message'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_code = hash('sha256', $ip_address); // 使用SHA-256哈希算法生成用户特征码
$parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

// 使用预处理语句防止SQL注入
$stmt = $conn->prepare("INSERT INTO messages (name, avatar, message, user_code, parent_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $name, $avatar, $message, $user_code, $parent_id);

if ($stmt->execute()) {
    echo "新记录插入成功";
} else {
    echo "错误: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
