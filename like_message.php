<?php

include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$messageId = $data['messageId'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_code = hash('sha256', $ip_address);

// 检查是否已经点赞
$stmt = $conn->prepare("SELECT * FROM likes WHERE message_id = ? AND user_code = ?");
$stmt->bind_param("is", $messageId, $user_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => '已经点赞过']);
    $stmt->close();
    $conn->close();
    exit();
}

// 插入点赞记录
$stmt = $conn->prepare("INSERT INTO likes (message_id, user_code) VALUES (?, ?)");
$stmt->bind_param("is", $messageId, $user_code);
$stmt->execute();

// 更新点赞数
$stmt = $conn->prepare("UPDATE messages SET likes = likes + 1 WHERE id = ?");
$stmt->bind_param("i", $messageId);
$stmt->execute();

// 获取最新的点赞数
$stmt = $conn->prepare("SELECT likes FROM messages WHERE id = ?");
$stmt->bind_param("i", $messageId);
$stmt->execute();
$result = $stmt->get_result();
$likes = $result->fetch_assoc()['likes'];

echo json_encode(['success' => true, 'likes' => $likes]);

$stmt->close();
$conn->close();
