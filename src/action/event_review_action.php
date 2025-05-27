<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';
include_once __DIR__ . '/../action/login_check.php';

$comment = $_GET['comment'];
$user_id = $_SESSION['user_id'];

$sql = "INSERT INTO event_comments (user_id, comment, created_at) VALUES ('$user_id', '$comment', NOW())";

if ($conn->query($sql)) {
    echo "<script>
        alert('이벤트 참여가 완료되었습니다!');
        window.location.href = '../event/event-review.php';
    </script>";
}
else {
    echo "<script>
        alert('댓글 등록 실패\\n에러: " . $conn->error . "');
        history.back();
    </script>";
}


?>