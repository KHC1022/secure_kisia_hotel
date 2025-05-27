<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $filename = basename($_FILES['profile_image']['name']);
    $target_dir = __DIR__ . '/../uploads/';
    $relative_path = '/uploads/' . $filename;
    $target_file = $target_dir . $filename;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 파일 업로드 전 기존 프로필 이미지 삭제
    $sql = "SELECT profile_image FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row['profile_image'] && $row['profile_image'] !== '/image/default_profile.jpg') {
        $old_file = __DIR__ . '/..' . $row['profile_image'];
        if (file_exists($old_file)) {
            unlink($old_file);
        }
    }

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
        $sql = "UPDATE users SET profile_image = '$relative_path' WHERE user_id = '$user_id'";
        mysqli_query($conn, $sql);
    }

    header("Location: ../user/mypage.php");
    exit;
}