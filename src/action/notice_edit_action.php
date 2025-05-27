<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$notice_id = $_GET['notice_id'];
$title = $_GET['title'];
$content = $_GET['content'];
$is_released = isset($_GET['is_released']) ? 1 : 0;


$sql = "UPDATE notices 
        SET title = '$title', 
        content = '$content', 
        is_released = $is_released, 
        created_at = NOW() 
        WHERE notice_id = '$notice_id'";
    
if (!$conn->query($sql)) {
    echo "<script>
            alert('공지사항 수정 실패: " . $conn->error . "');
            history.back();
            </script>";
    exit;
}

echo "<script>
        alert('공지사항이 성공적으로 수정되었습니다.');
        window.location.href = '../admin/admin.php?tab=notices';
        </script>";
?> 