<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

// 요청 방식 확인
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo "<script>
            alert('잘못된 요청 방식입니다.');
            history.back();
          </script>";
    exit;
}

// 입력값 필터링
$notice_id = isset($_POST['notice_id']) ? (int)$_POST['notice_id'] : 0;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$is_released = isset($_POST['is_released']) ? 1 : 0;

// 필수값 확인
if ($notice_id < 1 || empty($title) || empty($content)) {
    echo "<script>
            alert('모든 항목을 입력해주세요.');
            history.back();
          </script>";
    exit;
}

$sql = "UPDATE notices 
        SET title = ?, 
            content = ?, 
            is_released = ?, 
            created_at = NOW() 
        WHERE notice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $title, $content, $is_released, $notice_id);

if ($stmt->execute()) {
    echo "<script>
            alert('공지사항이 성공적으로 수정되었습니다.');
            window.location.href = '../admin/admin.php?tab=notices';
          </script>";
} else {
    error_log("공지사항 수정 실패: " . $stmt->error);
    echo "<script>
            alert('공지사항 수정에 실패했습니다.');
            history.back();
          </script>";
}

$stmt->close();
?>
