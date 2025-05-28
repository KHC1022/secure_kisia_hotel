<?php
include_once __DIR__ . '/../includes/session.php';
include_once __DIR__ . '/../includes/db_connection.php';

$inquiry_id = isset($_POST['inquiry_id']) ? (int)$_POST['inquiry_id'] : 0;
$category = trim($_POST['category'] ?? '');
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($inquiry_id < 1 || !$category || !$title || !$content) {
    echo "<script>alert('입력 값이 유효하지 않습니다.'); history.back();</script>";
    exit;
}

// 게시글 수정
$stmt = $conn->prepare("UPDATE inquiries SET category = ?, title = ?, content = ?, created_at = NOW() WHERE inquiry_id = ?");
$stmt->bind_param("sssi", $category, $title, $content, $inquiry_id);
$update_result = $stmt->execute();

if (!$update_result) {
    echo "<script>alert('수정 실패'); history.back();</script>";
    exit;
}

// 파일 업로드 처리
if (isset($_FILES['files']) && is_array($_FILES['files']['name']) && $_FILES['files']['name'][0] !== '') {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'txt', 'zip', 'docx'];
    $allowed_mime = ['image/jpeg', 'image/png', 'application/pdf', 'text/plain', 'application/zip', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

    // 기존 파일 삭제
    $select_stmt = $conn->prepare("SELECT file_path FROM inquiry_files WHERE inquiry_id = ?");
    $select_stmt->bind_param("i", $inquiry_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $abs_path = realpath(__DIR__ . '/../' . $row['file_path']);
        if ($abs_path && file_exists($abs_path)) {
            unlink($abs_path);
        }
    }

    // DB 내 기존 파일 삭제
    $del_stmt = $conn->prepare("DELETE FROM inquiry_files WHERE inquiry_id = ?");
    $del_stmt->bind_param("i", $inquiry_id);
    $del_stmt->execute();

    // 업로드 디렉터리 확인
    $upload_dir = realpath(__DIR__ . '/../uploads');
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }

    foreach ($_FILES['files']['name'] as $i => $original_name) {
        $tmp_name = $_FILES['files']['tmp_name'][$i];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $mime = mime_content_type($tmp_name);

        if (!in_array($ext, $allowed_ext) || !in_array($mime, $allowed_mime)) {
            echo "<script>alert('허용되지 않은 파일 형식입니다.'); history.back();</script>";
            exit;
        }

        $new_name = uniqid('file_', true) . '.' . $ext;
        $target_path = $upload_dir . '/' . $new_name;
        $db_path = 'uploads/' . $new_name;

        if (move_uploaded_file($tmp_name, $target_path)) {
            $insert_stmt = $conn->prepare("INSERT INTO inquiry_files (inquiry_id, file_name, file_path) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("iss", $inquiry_id, $original_name, $db_path);
            $insert_stmt->execute();
        } else {
            echo "<script>alert('파일 업로드 실패'); history.back();</script>";
            exit;
        }
    }
}

echo "<script>alert('수정되었습니다.'); location.href='../inquiry/inquiry_detail.php?inquiry_id={$inquiry_id}';</script>";
exit;
