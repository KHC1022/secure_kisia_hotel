<?php
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../action/admin_access.php';

// POST 요청인지 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

// 입력값 검증
$inquiry_id = isset($_POST['inquiry_id']) ? (int)$_POST['inquiry_id'] : 0;
$content = trim($_POST['content'] ?? '');
$admin_id = $_SESSION['user_id'] ?? 0;

if (strlen($content) > 5000) {
    echo "<script>alert('답변 내용은 5000자 이내로 작성해주세요.'); history.back();</script>";
    exit;
}

if (count($_POST) > 1000) {
    echo "<script>alert('입력 항목이 너무 많습니다.'); history.back();</script>";
    exit;
}

// 기존 답변 존재 여부 확인
$check_stmt = $conn->prepare("SELECT 1 FROM inquiry_responses WHERE inquiry_id = ?");
if (!$check_stmt) {
    error_log("쿼리 준비 실패 (select): " . $conn->error);
    echo "<script>alert('답변 처리 중 오류가 발생했습니다.'); history.back();</script>";
    exit;
}
$check_stmt->bind_param("i", $inquiry_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // 기존 답변 수정
    $update_stmt = $conn->prepare("UPDATE inquiry_responses SET content = ?, created_at = NOW() WHERE inquiry_id = ?");
    if (!$update_stmt) {
        error_log("쿼리 준비 실패 (update): " . $conn->error);
        echo "<script>alert('답변 처리 중 오류가 발생했습니다.'); history.back();</script>";
        exit;
    }
    $update_stmt->bind_param("si", $content, $inquiry_id);
    $result = $update_stmt->execute();
    $update_stmt->close();
} else {
    // 신규 답변 삽입 (admin_id 포함)
    $insert_stmt = $conn->prepare("INSERT INTO inquiry_responses (inquiry_id, admin_id, content, created_at) VALUES (?, ?, ?, NOW())");
    if (!$insert_stmt) {
        error_log("쿼리 준비 실패 (insert): " . $conn->error);
        echo "<script>alert('답변 처리 중 오류가 발생했습니다.'); history.back();</script>";
        exit;
    }
    $insert_stmt->bind_param("iis", $inquiry_id, $admin_id, $content);
    $result = $insert_stmt->execute();
    $insert_stmt->close();
}

$check_stmt->close();

// 결과 처리
if ($result) {
    echo "<script>alert('답변이 저장되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id={$inquiry_id}';</script>";
} else {
    error_log("답변 처리 실패: " . $conn->error);
    echo "<script>alert('답변 처리 중 오류가 발생했습니다.'); history.back();</script>";
}
?>