<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$inquiry_id = $_GET['inquiry_id'] ?? 0;
$content = $_GET['content'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

// 관리자 여부 확인
$admin_check = "SELECT user_id FROM users WHERE user_id = $user_id AND is_admin = 1";
$admin_result = mysqli_query($conn, $admin_check);
$admin = mysqli_fetch_assoc($admin_result);

if ($inquiry_id && $content && $admin) {
    $query = "INSERT INTO inquiry_responses (inquiry_id, admin_id, content, created_at) 
              VALUES ($inquiry_id, {$admin['user_id']}, '$content', NOW())";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('답변이 등록되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id=$inquiry_id';</script>";
    } else {
        echo mysqli_error($conn);
    }
} else {
    if (!$admin) {
        echo "<script>alert('관리자 권한이 필요합니다.'); history.back();</script>";
    } else {
        echo "<script>alert('내용을 입력해주세요.'); history.back();</script>";
    }
}