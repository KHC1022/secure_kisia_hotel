<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

// ✅ 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='../user/login.php';</script>";
    exit;
}

// ✅ 요청 방식 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
    exit;
}

// ✅ CSRF 토큰 검증
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
    exit;
}

// ✅ 입력값 확인 및 필터링
$comment = trim($_POST['comment'] ?? '');
$user_id = (int)$_SESSION['user_id'];

if (empty($comment)) {
    echo "<script>alert('댓글을 입력해주세요.'); history.back();</script>";
    exit;
}

// ✅ Prepared Statement로 안전하게 DB 삽입
$stmt = $conn->prepare("INSERT INTO event_comments (user_id, comment, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $user_id, $comment);

if ($stmt->execute()) {
    echo "<script>
        alert('이벤트 참여가 완료되었습니다!');
        window.location.href = '../event/event-review.php';
    </script>";
} else {
    error_log("댓글 등록 실패: " . $stmt->error);
    echo "<script>
        alert('댓글 등록에 실패했습니다.');
        history.back();
    </script>";
}

$stmt->close();
?>
