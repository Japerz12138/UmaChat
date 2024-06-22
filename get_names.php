<?php

include 'db_connect.php';

$sql = "SELECT name, avatar FROM avatars";
$result = $conn->query($sql);

$names = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $names[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($names);

$conn->close();