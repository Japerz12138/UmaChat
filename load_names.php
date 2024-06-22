<?php

require 'db_connect.php'; // 确保这个文件路径正确

$query = "SELECT name, avatar FROM avatars ORDER BY name ASC";
$result = $conn->query($query);

$names = [];
while ($row = $result->fetch_assoc()) {
    $names[] = $row;
}

echo json_encode($names);

$conn->close();
?>
