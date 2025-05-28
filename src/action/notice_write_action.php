<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

// GET이 아닌 경우 차단
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo "<script>
            alert('잘못된 요청입니다.');
            history.back();
          </script>";
    exit;
}

// 입력값 필터링 및 유효성 검사
$title = trim($_GET['title'] ?? '');
$content = trim($_GET['content'] ?? '');
$is_released = isset($_GET['is_released']) ? 1 : 0;
$user_id = (int)$_SESSION['user_id'];

if (empty($title) || empty($content)) {
    echo "<script>
            alert('제목과 내용을 모두 입력해주세요.');
            history.back();
          </script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO notices (user_id, title, content, is_released, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("issi", $user_id, $title, $content, $is_released);

if ($stmt->execute()) {
    echo "<script>
            alert('공지사항이 성공적으로 등록되었습니다.');
            window.location.href = '../admin/admin.php?tab=notices';
          </script>";
} else {
    error_log("공지사항 등록 실패: " . $stmt->error);
    echo "<script>
            alert('공지사항 등록에 실패했습니다.');
            history.back();
          </script>";
}

$stmt->close();
?>
