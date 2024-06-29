<?php

include 'db_connect.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$messages_per_page = 20;
$offset = ($page - 1) * $messages_per_page;

// 获取所有被封禁的用户特征码
$banned_result = $conn->query("SELECT user_code FROM banned_users");
$banned_users = array();
while ($row = $banned_result->fetch_assoc()) {
    $banned_users[] = $row['user_code'];
}

function getReplies($messageId, $level = 1) {
    global $conn, $banned_users;

    if ($level > 3) {
        return [];
    }

    $sql = "SELECT * FROM messages WHERE parent_id = ?";
    if (count($banned_users) > 0) {
        $placeholders = implode(',', array_fill(0, count($banned_users), '?'));
        $sql .= " AND user_code NOT IN ($placeholders)";
    }
    $sql .= " ORDER BY timestamp ASC";

    $stmt = $conn->prepare($sql);

    if (count($banned_users) > 0) {
        $params = array_merge([$messageId], $banned_users);
        $types = str_repeat('s', count($banned_users));
        $stmt->bind_param("i$types", ...$params);
    } else {
        $stmt->bind_param('i', $messageId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $replies = array();
    while ($row = $result->fetch_assoc()) {
        $replies[] = array_merge($row, ['replies' => getReplies($row['id'], $level + 1)]);
    }

    return $replies;
}

$banned_users_placeholder = implode(',', array_fill(0, count($banned_users), '?'));
$sql = "SELECT * FROM messages WHERE parent_id IS NULL";
if (count($banned_users) > 0) {
    $sql .= " AND user_code NOT IN ($banned_users_placeholder)";
}
$sql .= " ORDER BY timestamp DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if (count($banned_users) > 0) {
    $params = array_merge($banned_users, [$messages_per_page, $offset]);
    $stmt->bind_param(str_repeat('s', count($banned_users)) . 'ii', ...$params);
} else {
    $stmt->bind_param('ii', $messages_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$messages = array();
while ($row = $result->fetch_assoc()) {
    $messages[] = array_merge($row, ['replies' => getReplies($row['id'])]);
}

// 获取一下总共的post数量
$count_sql = "SELECT COUNT(*) as total FROM messages WHERE parent_id IS NULL";
if (count($banned_users) > 0) {
    $count_sql .= " AND user_code NOT IN ($banned_users_placeholder)";
}
$count_stmt = $conn->prepare($count_sql);

if (count($banned_users) > 0) {
    $count_stmt->bind_param(str_repeat('s', count($banned_users)), ...$banned_users);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_messages = $count_result->fetch_assoc()['total'];

$response = array(
    'messages' => $messages,
    'total_messages' => $total_messages,
    'messages_per_page' => $messages_per_page,
    'current_page' => $page
);

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
