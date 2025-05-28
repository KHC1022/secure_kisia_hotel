<?php
include_once __DIR__ . '/db_connection.php';

// 공지사항 ID 확인 및 정수 필터링
if (!isset($_GET['notice_edit']) || !is_numeric($_GET['notice_edit'])) {
    echo "<script>
            alert('잘못된 접근입니다.');
            window.location.href = '../admin/admin.php?tab=notices';
          </script>";
    exit;
}

$notice_id = (int)$_GET['notice_edit'];

// 공지사항 정보 가져오기
$sql = "SELECT n.*, u.username 
        FROM notices n 
        LEFT JOIN users u ON n.user_id = u.user_id 
        WHERE n.notice_id = $notice_id";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<script>
            alert('존재하지 않는 공지사항입니다.');
            window.location.href = '../admin/admin.php?tab=notices';
          </script>";
    exit;
}

$notice = $result->fetch_assoc();

// 공개 여부 체크박스 상태
$is_released_checked = ($notice['is_released']) ? 'checked' : '';
?>
