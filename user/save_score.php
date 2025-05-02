<?php
require_once '../common/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $score = isset($data['score']) ? intval($data['score']) : 0;
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : $_SESSION['user_id'];

    $subject_id = isset($data['subject_id']) ? intval($data['subject_id']) : 0;
    $sql = "INSERT INTO quizzes (user_id, subject_id, score, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $subject_id, $score);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
}

$conn->close();
?>