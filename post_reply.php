<?php

include 'db_connect.php';

$name = $_POST['name'];
$avatar = $_POST['avatar'];
$message = $_POST['message'];
$parent_id = $_POST['parent_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_code = hash('sha256', $ip_address); // 使用SHA-256哈希算法生成用户特征码

// 检查是否超过回复数量限制
$stmt = $conn->prepare("SELECT COUNT(*) as reply_count FROM messages WHERE parent_id = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$reply_count = $result->fetch_assoc()['reply_count'];

if ($reply_count >= 8) {
    echo "回复最多允许八条";
} else {
    // 使用预处理语句防止SQL注入
    $stmt = $conn->prepare("INSERT INTO messages (name, avatar, message, user_code, parent_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $avatar, $message, $user_code, $parent_id);

    if ($stmt->execute()) {
        echo "新回复插入成功";
    } else {
        echo "错误: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
