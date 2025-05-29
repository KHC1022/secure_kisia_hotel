<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/admin_access.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 접근 방식입니다.'); history.back();</script>";
    exit;
}

$inquiry_id = isset($_POST['inquiry_id']) ? (int)$_POST['inquiry_id'] : 0;
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'] ?? 0;

if ($inquiry_id <= 0 || empty($content) || !$user_id) {
    echo "<script>alert('필수 정보가 누락되었습니다.'); history.back();</script>";
    exit;
}

// 관리자 여부 확인
$admin_check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND is_admin = 1");
$admin_check_stmt->bind_param("i", $user_id);
$admin_check_stmt->execute();
$admin_result = $admin_check_stmt->get_result();
$admin = $admin_result->fetch_assoc();

if (!$admin) {
    echo "<script>alert('관리자 권한이 필요합니다.'); history.back();</script>";
    exit;
}

// 답변 등록
$stmt = $conn->prepare("INSERT INTO inquiry_responses (inquiry_id, admin_id, content, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iis", $inquiry_id, $user_id, $content);

if ($stmt->execute()) {
    echo "<script>alert('답변이 등록되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id={$inquiry_id}';</script>";
} else {
    error_log("답변 등록 실패: " . $stmt->error);
    echo "<script>alert('답변 등록 중 오류가 발생했습니다.'); history.back();</script>";
}
?>
