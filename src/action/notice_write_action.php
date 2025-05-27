<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 공지사항 기본 정보
    $title = $_GET['title'];
    $content = $_GET['content'];
    $user_id = $_SESSION['user_id'];
    $is_released = isset($_GET['is_released']) ? 1 : 0;  // 체크박스가 체크되어 있으면 1, 아니면 0

    // 공지사항 등록
    $sql = "INSERT INTO notices (user_id, title, content, is_released, created_at) 
            VALUES ('$user_id', '$title', '$content', $is_released, NOW())";
    
    if (!$conn->query($sql)) {
        echo "<script>
                alert('공지사항 등록 실패: " . $conn->error . "');
                history.back();
              </script>";
        exit;
    }

    echo "<script>
            alert('공지사항이 성공적으로 등록되었습니다.');
            window.location.href = '../admin/admin.php?tab=notices';
          </script>";
}
?> 