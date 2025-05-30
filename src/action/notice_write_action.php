<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

// 관리자가 아닌 경우 CSRF 토큰 검증
if (!isset($_SESSION['is_admin'])) {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
      echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
      exit;
  }
}

// POST 요청인지 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>
            alert('잘못된 요청입니다.');
            history.back();
          </script>";
    exit;
}

// 입력값 필터링 및 유효성 검사
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$is_released = isset($_POST['is_released']) ? 1 : 0;
$user_id = (int)$_SESSION['user_id'];

if (empty($title) || empty($content)) {
    echo "<script>
            alert('제목과 내용을 모두 입력해주세요.');
            history.back();
          </script>";
    exit;
}

if (strlen($title) > 100) {
  echo "<script>alert('제목은 100자 이내로 입력해주세요.'); history.back();</script>";
  exit;
}
if (strlen($content) > 5000) {
  echo "<script>alert('내용은 5000자 이내로 입력해주세요.'); history.back();</script>";
  exit;
}
if (count($_POST) > 1000) {
  echo "<script>alert('입력 항목 수가 너무 많습니다.'); history.back();</script>";
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
