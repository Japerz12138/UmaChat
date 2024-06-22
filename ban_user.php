<?php

include 'db_connect.php';

$user_code = $_POST['user_code'];

// 将用户特征码添加到banned_users表
$stmt = $conn->prepare("INSERT INTO banned_users (user_code) VALUES (?)");
$stmt->bind_param("s", $user_code);

if ($stmt->execute()) {
    // 删除messages表中该用户的所有信息
    $delete_stmt = $conn->prepare("DELETE FROM messages WHERE user_code = ?");
    $delete_stmt->bind_param("s", $user_code);
    $delete_stmt->execute();
    echo "用户已封禁且信息已删除";
} else {
    echo "错误: " . $stmt->error;
}

$stmt->close();
$conn->close();
