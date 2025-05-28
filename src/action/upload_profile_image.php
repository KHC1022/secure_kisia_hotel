<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['profile_image']['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        exit('허용되지 않는 파일 형식입니다.');
    }

    $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    $new_filename = 'user' . $user_id . '_' . bin2hex(random_bytes(8)) . '.' . $ext;

    $target_dir = __DIR__ . '/../uploads/';
    $relative_path = '/uploads/' . $new_filename;
    $target_file = $target_dir . $new_filename;

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // 기존 이미지 삭제
    $stmt = $conn->prepare("SELECT profile_image FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['profile_image']) && $row['profile_image'] !== '/image/default_profile.jpg') {
            $old_file = __DIR__ . '/..' . $row['profile_image'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }
    }

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
        $stmt->bind_param('si', $relative_path, $user_id);
        $stmt->execute();
    }

    header("Location: ../user/mypage.php");
    exit;
}
?>
