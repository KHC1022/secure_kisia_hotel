<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

$inquiry_id = isset($_POST['inquiry_id']) ? (int)$_POST['inquiry_id'] : 0;
$content = trim($_POST['content'] ?? '');

if ($inquiry_id <= 0 || empty($content)) {
    echo "<script>alert('필수 항목이 누락되었습니다.'); history.back();</script>";
    exit;
}

// 기존 답변 존재 여부 확인
$check_stmt = $conn->prepare("SELECT 1 FROM inquiry_responses WHERE inquiry_id = ?");
$check_stmt->bind_param("i", $inquiry_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // 답변 수정
    $update_stmt = $conn->prepare("UPDATE inquiry_responses SET content = ?, updated_at = NOW() WHERE inquiry_id = ?");
    $update_stmt->bind_param("si", $content, $inquiry_id);
    $result = $update_stmt->execute();
} else {
    // 답변 새로 등록
    $insert_stmt = $conn->prepare("INSERT INTO inquiry_responses (inquiry_id, content, created_at) VALUES (?, ?, NOW())");
    $insert_stmt->bind_param("is", $inquiry_id, $content);
    $result = $insert_stmt->execute();
}

if ($result) {
    echo "<script>alert('답변이 수정되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id={$inquiry_id}';</script>";
} else {
    error_log("답변 수정 실패: " . $conn->error);
    echo "<script>alert('답변 처리 중 오류가 발생했습니다.'); history.back();</script>";
}
?>
