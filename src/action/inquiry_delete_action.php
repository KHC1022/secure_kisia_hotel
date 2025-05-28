<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 요청 방식입니다.'); history.back();</script>";
    exit;
}

$inquiry_id = isset($_POST['inquiry_id']) ? (int)$_POST['inquiry_id'] : 0;

if ($inquiry_id < 1) {
    echo "<script>alert('유효하지 않은 요청입니다.'); history.back();</script>";
    exit;
}

// 존재 여부 확인
$check_stmt = $conn->prepare("SELECT 1 FROM inquiries WHERE inquiry_id = ?");
$check_stmt->bind_param("i", $inquiry_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    echo "<script>alert('존재하지 않는 문의글입니다.'); history.back();</script>";
    exit;
}

// 삭제 실행
$delete_stmt = $conn->prepare("DELETE FROM inquiries WHERE inquiry_id = ?");
$delete_stmt->bind_param("i", $inquiry_id);
$success = $delete_stmt->execute();

if ($success) {
    echo "<script>alert('삭제되었습니다.'); location.href='../inquiry/inquiry.php';</script>";
} else {
    error_log("삭제 실패: " . $conn->error);
    echo "<script>alert('삭제 실패했습니다.'); history.back();</script>";
}
?>
