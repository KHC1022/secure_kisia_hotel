<?php 
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

// notice_id 존재 여부 및 유효성 검사
if (!isset($_GET['notice_id']) || !is_numeric($_GET['notice_id'])) {
    echo "<script>
            alert('잘못된 접근입니다.'); history.back();
          </script>";
    exit;
}

$notice_id = (int)$_GET['notice_id'];

// 공지사항 정보 조회 쿼리 (Prepared Statement 사용)
$query = "SELECT n.*, u.username 
          FROM notices n
          JOIN users u ON n.user_id = u.user_id
          WHERE n.notice_id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "<script>alert('쿼리 준비 실패: {$conn->error}'); history.back();</script>";
    exit;
}

$stmt->bind_param('i', $notice_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
            alert('존재하지 않는 공지사항입니다.'); history.back();
          </script>";
    exit;
}


$notice = $result->fetch_assoc();

// 일반 유저 비공개 공지사항 접근 제한
if (!$notice['is_released']) {
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
        echo "<script>
            alert('존재하지 않는 공지사항입니다.'); history.back();
          </script>";
        exit;
    }
}

$GLOBALS['notice'] = $notice;
?>