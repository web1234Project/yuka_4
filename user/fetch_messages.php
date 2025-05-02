<?php
session_start();
require_once '../common/config.php';

if (!isset($_POST['report_id'])) {
    echo "[]"; // return empty
    exit;
}

$report_id = $_POST['report_id'];

// Get user_id
$stmt = $conn->prepare("SELECT report_created FROM report WHERE report_id = ?");
$stmt->bind_param("s", $report_id);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

$messages = [];

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ");
    $stmt->bind_param("ssss", $report_id, $user_id, $user_id, $report_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
}

echo json_encode($messages);
$conn->close();
?>
